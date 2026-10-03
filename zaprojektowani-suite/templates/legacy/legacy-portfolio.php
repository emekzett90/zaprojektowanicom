<?php
/**
 * ZAPROJEKTOWANI — SHOWCASE PORTFOLIO / WEB + LOGO SWITCH v9.2 HOME PORTFOLIO MODAL WEB-TOP LUCIDE FIX
 * Shortcode: [zp_showcase_portfolio]
 *
 * v8.4:
 * - mobile: usunięty programatyczny snap po scrollu raila, żeby nie wywoływać skoków góra/dół,
 * - mobile: przełączanie WEB/LOGO bez window.scrollTo i bez kompensacji viewportu,
 * - mobile: stała wysokość sceny portfolio, żeby zmiana na kwadratowe logo nie zmieniała wysokości sekcji,
 * - mobile: strzałki przesuwają o 1 kartę, ale bez dotykania pionowego scrolla strony.
 *
 * v8.1:
 * - mobile: twarde wyłączenie skoków przy powrocie/scrollu przez overflow-anchor:none,
 * - mobile: bez transform/opacity na stage podczas przełączania WEB/LOGO,
 * - mobile: przełączanie WEB/LOGO zachowuje pozycję viewportu i nie przewija strony,
 * - mobile: mocniejszy touch/click guard na railu, żeby drag nie odpalał linków ani nowej karty,
 * - mobile: resize z paska adresu ignorowany całkowicie, poza realną zmianą orientacji/szerokości.
 *
 * v7.9:
 * - mobile: mocniejszy guard drag/click, żeby poziome przesuwanie nie otwierało linków ani nowych kart,
 * - mobile: płynniejsze przewijanie raila i stabilniejszy viewport bez skoków do góry,
 * - mobile: większe/luźniejsze buttony w kartach,
 * - usunięty napis SHOWCASE z okienka CTA „Tu może być Twój projekt”, zostaje tylko watermark długiej sekcji.
 *
 * v7.8:
 * - desktop smooth scroll: zoptymalizowany poziomy rail, mniej layout read/write w każdej klatce,
 * - cache pozycji kart zamiast liczenia offsetów przy każdym ticku,
 * - progres i aktywna karta aktualizowane tylko gdy realnie się zmieniają,
 * - stabilniejsze requestAnimationFrame + lżejsze transformy GPU, mniej zacięć przy przewijaniu.
 *
 * v8.2:
 * - mobile: poziome przewijanie przeskakuje równo po 1 projekcie / 1 viewport,
 * - mobile: strzałki prev/next przesuwają dokładnie o jedną kartę,
 * - mobile: snap po zakończeniu gestu bez przewijania strony do góry,
 * - mobile: zachowany hard guard przed przypadkowym otwieraniem linków podczas swipe.
 *
 * v8.3:
 * - ciemna, podłużna sekcja CTA zostaje w shortcode na desktopie, ale jest twardo ukryta na mobile,
 * - przygotowany osobny HTML widget tej sekcji do wklejenia jako niezależny blok,
 * - mobile portfolio zostaje bez dodatkowego ciężkiego CTA pod spodem, żeby ograniczyć skoki scrolla.
 *
 * v7.7:
 * - powiększone karty logo o ok. 30% na desktop/tablet,
 * - switcher WEB / LOGO ma większą typografię (+7px),
 * - button hover naprawiony: ciemne wypełnienie idzie pełną warstwą bez białych prześwitów,
 * - ciemna karta CTA dostała duży napis SHOWCASE w stylu globalnych watermarków.
 *
 * v7.6:
 * - główny desktopowy switcher zostaje dokładnie w swoim miejscu w nagłówku,
 * - gdy showcase wchodzi w tryb scroll/flow i nagłówek znika, pod floating progress barem pojawia się drugi switcher,
 * - floating switcher korzysta z tego samego mechanizmu data-zp-mode-btn, więc płynnie przełącza WEB / LOGO,
 * - floating panel ma pointer-events tylko podczas is-flowing, więc nie blokuje kart poza aktywnym trybem,
 * - mobile bez zmian: zwykły natywny rail i switcher w nagłówku mobilnym.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!function_exists('zp_showcase_portfolio_shortcode')) {
  function zp_showcase_portfolio_shortcode() {
    ob_start();

    $zp_mobile_web_items = zp_suite_portfolio_items('web');
    $zp_mobile_logo_items = zp_suite_portfolio_items('logo');

    $zp_render_mobile_card = function($item, $index, $mode) {
      $is_cta = !empty($item['isCta']);
      $brand = $item['brand'] ?? '';
      $sub = $item['sub'] ?? '';
      $type = $item['type'] ?? '';
      $img = $item['img'] ?? '';
      $meta = is_array($item['meta'] ?? null) ? $item['meta'] : [];
      $scope = is_array($item['scope'] ?? null) ? $item['scope'] : [];
      $desc = $item['desc'] ?? '';
      $live = $item['live'] ?? '';
      $num = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
      /* v2.2.709 mobile stability: do not decode every portfolio image at once.
         The real image is hydrated only for the current card and its neighbours. */
      $mobile_placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 10'%3E%3Crect width='16' height='10' fill='%2305070b'/%3E%3C/svg%3E";
      $mobile_img = function_exists('zp_suite_full_upload_image_url') ? zp_suite_full_upload_image_url($img) : $img;

      if ($is_cta) {
        ob_start(); ?>
        <article class="zpShowcaseMobile__card zpShowcaseMobile__card--cta">
          <div class="zpShowcaseMobile__ctaFrame">
            <span>Twój projekt może być następny</span>
            <h3><?php echo esc_html($mode === 'logo' ? 'Tu może być Twoje logo' : 'Tu może być Twoja strona'); ?></h3>
            <p><?php echo esc_html($mode === 'logo' ? 'Opisz markę — przygotujemy logo, system identyfikacji, mockupy i materiały firmowe.' : 'Opisz projekt — dobierzemy zakres, styl, funkcje, SEO i plan wdrożenia.'); ?></p>
            <a href="/studio-wyceny/">Otrzymaj darmową wycenę</a>
          </div>
        </article>
        <?php return ob_get_clean();
      }

      ob_start(); ?>
      <article class="zpShowcaseMobile__card" data-zp-mobile-card>
        <div class="zpShowcaseMobile__media">
          <img class="zpShowcaseMobile__img" src="<?php echo esc_attr($mobile_placeholder); ?>" data-zp-mobile-src="<?php echo esc_url($mobile_img); ?>" data-zp-mobile-loaded="0" alt="<?php echo esc_attr($brand . ' — ' . $sub); ?>" loading="lazy" decoding="async" fetchpriority="low" draggable="false" sizes="(max-width: 720px) 92vw, 520px">
          <span class="zpShowcaseMobile__shade" aria-hidden="true"></span>
        </div>
        <div class="zpShowcaseMobile__top">
          <span><?php echo esc_html($type); ?></span>
          <i><?php echo esc_html($num); ?></i>
        </div>
        <div class="zpShowcaseMobile__content">
          <h3><?php echo esc_html($brand); ?><small><?php echo esc_html($sub); ?></small></h3>
          <?php if (!empty($meta)) : ?>
            <div class="zpShowcaseMobile__meta">
              <?php foreach (array_slice($meta, 0, 4) as $m) : ?><span><?php echo esc_html($m); ?></span><?php endforeach; ?>
            </div>
          <?php endif; ?>
          <div class="zpShowcaseMobile__actions">
            <button class="zpShowcaseMobile__detailsBtn" type="button" data-zp-mobile-details><span>Szczegóły</span><i data-lucide="chevron-down" aria-hidden="true"></i></button>
            <?php if ($live) : ?><a class="zpShowcaseMobile__liveBtn" href="<?php echo esc_url($live); ?>" target="_blank" rel="noopener"><span>Zobacz live</span><i data-lucide="external-link" aria-hidden="true"></i></a><?php endif; ?>
          </div>
          <div class="zpShowcaseMobile__details" hidden>
            <?php if ($desc) : ?><p><?php echo esc_html($desc); ?></p><?php endif; ?>
            <?php if (!empty($scope)) : ?>
              <ul><?php foreach (array_slice($scope, 0, 5) as $s) : ?><li><?php echo esc_html($s); ?></li><?php endforeach; ?></ul>
            <?php endif; ?>
            <div class="zpShowcaseMobile__links">
              <a href="/studio-wyceny/">Zamów podobny projekt</a>
            </div>
          </div>
        </div>
      </article>
      <?php return ob_get_clean();
    };
    ?>

<section class="zpShowcaseWhite" id="zpShowcaseWhite" data-zp-showcase-white data-mode="web" aria-labelledby="zpShowcaseWhiteTitle">
  <div class="zpShowcaseWhite__spacer" data-zp-showcase-spacer>
    <div class="zpShowcaseWhite__sticky" data-zp-showcase-sticky>
      <div class="zpShowcaseWhite__inner">

        <header class="zpShowcaseWhite__head" data-zp-showcase-head>
          <div class="zpShowcaseWhite__copy">
            <span class="zpShowcaseWhite__kicker" data-zp-showcase-kicker>Portfolio / realizacje</span>

            <h2 id="zpShowcaseWhiteTitle" class="zpShowcaseWhite__title" data-zp-showcase-title>
              <strong>Strony, sklepy i systemy,</strong>
              <span>które realnie pracują</span>
              na wizerunek i sprzedaż.
            </h2>

            <p class="zpShowcaseWhite__lead" data-zp-showcase-lead>
              Zobacz wybrane projekty, które łączą projekt graficzny, WordPress, WooCommerce,
              SEO, analitykę, rezerwacje, automatyzacje i kampanie w jeden dopracowany system.
            </p>

            <div class="zpShowcaseWhite__switch zpShowcaseWhite__switch--mobile" role="tablist" aria-label="Przełącz typ realizacji">
              <button type="button" class="zpShowcaseWhite__switchBtn is-active" data-zp-mode-btn="web" role="tab" aria-selected="true">
                <i class="zpShowcaseWhite__switchIcon" data-lucide="monitor" aria-hidden="true"></i>
                <span>Strony WWW</span>
              </button>
              <button type="button" class="zpShowcaseWhite__switchBtn" data-zp-mode-btn="logo" role="tab" aria-selected="false">
                <i class="zpShowcaseWhite__switchIcon" data-lucide="pen-tool" aria-hidden="true"></i>
                <span>Branding</span>
              </button>
              <i class="zpShowcaseWhite__switchThumb" aria-hidden="true"></i>
              <span class="zpShowcaseWhite__tapHint" aria-hidden="true"><i class="zpShowcaseWhite__tapHand" data-lucide="hand"></i><i class="zpShowcaseWhite__tapArrow" data-lucide="arrow-right"></i></span>
            </div>
          </div>

          <aside class="zpShowcaseWhite__side">
            <div class="zpShowcaseWhite__switch zpShowcaseWhite__switch--desktop" role="tablist" aria-label="Przełącz typ realizacji">
              <button type="button" class="zpShowcaseWhite__switchBtn is-active" data-zp-mode-btn="web" role="tab" aria-selected="true">
                <i class="zpShowcaseWhite__switchIcon" data-lucide="monitor" aria-hidden="true"></i>
                <span>Strony WWW</span>
              </button>
              <button type="button" class="zpShowcaseWhite__switchBtn" data-zp-mode-btn="logo" role="tab" aria-selected="false">
                <i class="zpShowcaseWhite__switchIcon" data-lucide="pen-tool" aria-hidden="true"></i>
                <span>Branding</span>
              </button>
              <i class="zpShowcaseWhite__switchThumb" aria-hidden="true"></i>
              <span class="zpShowcaseWhite__tapHint" aria-hidden="true"><i class="zpShowcaseWhite__tapHand" data-lucide="hand"></i><i class="zpShowcaseWhite__tapArrow" data-lucide="arrow-right"></i></span>
            </div>

            <div class="zpShowcaseWhite__progress">
              <span data-zp-showcase-current>01</span>
              <i><b data-zp-showcase-bar></b></i>
              <span data-zp-showcase-total>10</span>
            </div>

            <p data-zp-showcase-tip>
              Przewiń w dół — realizacje przejdą poziomo. Możesz też przełączyć widok na realizacje logo i brandingu.
            </p>
          </aside>
        </header>

        <div class="zpShowcaseMobileStable" data-zp-mobile-stable-portfolio data-mode="web" aria-label="Mobilne portfolio Zaprojektowani">
          <div class="zpShowcaseMobileStable__switch" role="tablist" aria-label="Przełącz typ realizacji mobilnie">
            <button type="button" class="is-active" data-zp-mobile-mode="web" role="tab" aria-selected="true"><i data-lucide="monitor" aria-hidden="true"></i><span>Strony WWW</span></button>
            <button type="button" data-zp-mobile-mode="logo" role="tab" aria-selected="false"><i data-lucide="pen-tool" aria-hidden="true"></i><span>Branding</span></button>
            <i aria-hidden="true"></i>
            <b class="zpShowcaseMobileStable__handHint" aria-hidden="true"><span>przełącz</span><svg class="zpShowcaseMobileStable__handSvg" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M8 13V5.5a1.5 1.5 0 0 1 3 0V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M11 12V4.5a1.5 1.5 0 0 1 3 0V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 12V6.5a1.5 1.5 0 0 1 3 0V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 13v-1.5a1.5 1.5 0 0 1 3 0V15c0 4-2.7 6-6.5 6H12c-2.5 0-4.2-1.1-5.6-3.1l-2-2.9a1.7 1.7 0 0 1 2.7-2l1.9 2.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></b>
          </div>

          <div class="zpShowcaseMobileStable__rails">
            <div class="zpShowcaseMobileStable__rail is-active" data-zp-mobile-rail="web"></div>
            <template data-zp-mobile-template="web">
              <?php foreach ($zp_mobile_web_items as $i => $item) { echo $zp_render_mobile_card($item, $i, 'web'); } ?>
            </template>
            <div class="zpShowcaseMobileStable__rail" data-zp-mobile-rail="logo" hidden></div>
            <template data-zp-mobile-template="logo">
              <?php foreach ($zp_mobile_logo_items as $i => $item) { echo $zp_render_mobile_card($item, $i, 'logo'); } ?>
            </template>
          </div>
        </div>

        <div class="zpShowcaseWhite__floatingProgress" data-zp-showcase-floating aria-hidden="false">
          <div class="zpShowcaseWhite__floatProgressLine">
            <span data-zp-showcase-current>01</span>
            <i><b data-zp-showcase-bar></b></i>
            <span data-zp-showcase-total>10</span>
          </div>

          <div class="zpShowcaseWhite__switch zpShowcaseWhite__switch--floating" role="tablist" aria-label="Przełącz typ realizacji podczas przeglądania portfolio">
            <button type="button" class="zpShowcaseWhite__switchBtn is-active" data-zp-mode-btn="web" role="tab" aria-selected="true">
              <i class="zpShowcaseWhite__switchIcon" data-lucide="monitor" aria-hidden="true"></i>
              <span>Strony WWW</span>
            </button>
            <button type="button" class="zpShowcaseWhite__switchBtn" data-zp-mode-btn="logo" role="tab" aria-selected="false">
              <i class="zpShowcaseWhite__switchIcon" data-lucide="pen-tool" aria-hidden="true"></i>
              <span>Branding</span>
            </button>
            <i class="zpShowcaseWhite__switchThumb" aria-hidden="true"></i>
          </div>
        </div>

        <div class="zpShowcaseWhite__stage" data-zp-showcase-stage>
          <div class="zpShowcaseWhite__rail" data-zp-showcase-rail></div>
        </div>

        <footer class="zpShowcaseWhite__bottom">
          <div class="zpShowcaseWhite__hint">
            <span data-zp-showcase-bottom-tip>Scrolluj dalej lub przełącz typ realizacji</span>
            <i></i>
          </div>

          <div class="zpShowcaseWhite__controls">
            <button type="button" class="zpShowcaseWhite__navBtn" data-zp-showcase-prev aria-label="Poprzednia realizacja">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </button>

            <button type="button" class="zpShowcaseWhite__navBtn" data-zp-showcase-next aria-label="Następna realizacja">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </button>
          </div>
        </footer>

      </div>
    </div>
  </div>
</section>

<section class="zpShowcaseDarkCta" id="zpShowcaseDarkCta" aria-labelledby="zpShowcaseDarkCtaTitle">
  <div class="zpShowcaseDarkCta__bleed" aria-hidden="true"></div>
  <div class="zpShowcaseDarkCta__mark" aria-hidden="true">zaprojektowani</div>

  <div class="zpShowcaseDarkCta__inner">
    <div class="zpShowcaseDarkCta__copy">
      <span class="zpShowcaseDarkCta__eyebrow">Twój projekt może być następny</span>

      <h2 id="zpShowcaseDarkCtaTitle">
        Zbudujmy stronę, sklep albo markę, która od pierwszego kontaktu wygląda profesjonalnie.
      </h2>

      <p>
        Opisz nam, co chcesz stworzyć. Dobierzemy zakres, kierunek wizualny,
        funkcje, SEO, analitykę, branding i plan wdrożenia — bez przypadkowych rozwiązań.
      </p>
    </div>

    <div class="zpShowcaseDarkCta__panel">
      <div class="zpShowcaseDarkCta__frame">
        <span>Tu może być Twój projekt!</span>
      </div>

      <div class="zpShowcaseDarkCta__actions">
        <a class="zpShowcaseDarkCta__btn" href="/studio-wyceny/">
          Otrzymaj darmową wycenę
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
        </a>

        <a class="zpShowcaseDarkCta__btn zpShowcaseDarkCta__btn--ghost" href="/strony-internetowe-katowice/">
          Zobacz ofertę
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<div class="zpShowcasePop" id="zpShowcasePop" role="dialog" aria-modal="true" aria-hidden="true" hidden aria-labelledby="zpShowcasePopTitle">
  <div class="zpShowcasePop__backdrop" data-zp-pop-close></div>

  <div class="zpShowcasePop__panel" role="document">
    <button class="zpShowcasePop__close" type="button" data-zp-pop-close aria-label="Zamknij szczegóły realizacji">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>

    <div class="zpShowcasePop__body" data-zp-pop-body></div>
  </div>
</div>

<style>.zpShowcaseWhite,.zpShowcaseWhite *,.zpShowcaseWhite *::before,.zpShowcaseWhite *::after,.zpShowcasePop,.zpShowcasePop *,.zpShowcasePop *::before,.zpShowcasePop *::after,.zpShowcaseDarkCta,.zpShowcaseDarkCta *,.zpShowcaseDarkCta *::before,.zpShowcaseDarkCta *::after{box-sizing: border-box;font-family: "Plus Jakarta Sans Local","Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;font-style: normal}.zpShowcaseWhite{--zp-black: #050505;--zp-muted: rgba(5,5,5,.58);--zp-line: rgba(5,5,5,.10);--zp-accent: #102a4f;--zp-accent2: #1c477a;--zp-accent3: #3b6ea8;--zp-dark: #05070b;--zp-grad: linear-gradient(90deg,var(--zp-accent),var(--zp-accent2),var(--zp-accent3));--zp-btn-grad: linear-gradient(90deg,#05070b 0%,#0b1830 42%,#102a4f 72%,#1c477a 100%);--zp-font: "Plus Jakarta Sans Local","Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;--zpShowcaseVH: 1vh;width: 100%;max-width: 100%;margin: 0;position: relative;background: #ffffff;color: var(--zp-black);font-family: var(--zp-font);overflow: visible;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;text-rendering: geometricPrecision;font-synthesis: none}.zpShowcaseWhite__spacer{min-height: 300vh;position: relative}.zpShowcaseWhite__sticky{position: sticky;top: 0;min-height: 100vh;min-height: 100svh;display: flex;align-items: center;overflow: hidden;background: #ffffff;transform: translateZ(0);backface-visibility: hidden;contain: layout paint}.zpShowcaseWhite__inner{width: 100%;max-width: 100%;margin: 0;padding: clamp(44px,4.8vw,78px) 0 clamp(30px,3vw,48px);position: relative;z-index: 2;background: #ffffff}.zpShowcaseWhite__head,.zpShowcaseWhite__bottom{width: min(1500px,calc(100% - 56px));margin-left: auto;margin-right: auto}.zpShowcaseWhite__head{display: grid;grid-template-columns: minmax(0,1fr) minmax(320px,460px);gap: clamp(24px,4vw,70px);align-items: end;justify-content: space-between;margin-bottom: clamp(26px,2.7vw,40px);opacity: 1;transform: translate3d(0,0,0);transition: opacity .42s cubic-bezier(.16,1,.3,1),transform .48s cubic-bezier(.16,1,.3,1),max-height .46s cubic-bezier(.16,1,.3,1),margin .46s cubic-bezier(.16,1,.3,1)}.zpShowcaseWhite.is-flowing .zpShowcaseWhite__head{opacity: 0;transform: translate3d(0,-26px,0);pointer-events: none}.zpShowcaseWhite__copy{min-width: 0}.zpShowcaseWhite__kicker,.zpShowcaseDarkCta__eyebrow,.zpShowcaseWhite__ctaEyebrow{display: inline-flex;width: max-content;align-items: center;gap: 10px;margin: 0 0 14px;color: rgba(5,5,5,.54);text-transform: uppercase;letter-spacing: .13em;font-size: 10px;line-height: 1;font-weight: 800}.zpShowcaseWhite__kicker::before,.zpShowcaseDarkCta__eyebrow::before,.zpShowcaseWhite__ctaEyebrow::before{content: "";display: block;width: 24px;height: 1px;background: var(--zp-grad)}.zpShowcaseWhite__title{max-width: 900px;margin: 0;color: #050505;font-size: clamp(34px,4.45vw,62px);line-height: .98;letter-spacing: -0.052em;font-weight: 560;font-variation-settings: "wght" 560;text-shadow: none;text-wrap: balance;transition: opacity .28s ease,transform .34s cubic-bezier(.16,1,.3,1)}.zpShowcaseWhite__title strong{display: inline;color: #050505;font: inherit;font-weight: 720;font-variation-settings: "wght" 720;letter-spacing: inherit}.zpShowcaseWhite__title span{display: inline;color: #071426;font-size: inherit;line-height: inherit;letter-spacing: inherit;font-weight: 620;font-variation-settings: "wght" 620}.zpShowcaseWhite__lead{max-width: 760px;margin: clamp(18px,2vw,24px) 0 0;color: rgba(5,5,5,.58);font-size: clamp(13px,1vw,15px);line-height: 1.62;letter-spacing: -0.006em;font-weight: 400;font-variation-settings: "wght" 400;transition: opacity .28s ease,transform .34s cubic-bezier(.16,1,.3,1)}.zpShowcaseWhite.is-switching .zpShowcaseWhite__title,.zpShowcaseWhite.is-switching .zpShowcaseWhite__lead{opacity: .2;transform: translate3d(0,8px,0)}.zpShowcaseWhite__side{border-top: 1px solid rgba(5,5,5,.10);border-bottom: 1px solid rgba(5,5,5,.10);padding: 18px 0 17px;background: transparent;width: 100%}.zpShowcaseWhite__switch{position: relative;width: 100%;display: grid;grid-template-columns: 1fr 1fr;gap: 0;margin: 0 0 18px;padding: 4px;border-radius: 999px;background: #f1f2f4;border: 1px solid rgba(5,5,5,.08);box-shadow: inset 0 1px 0 rgba(255,255,255,.92),0 14px 34px rgba(7,20,38,.055);overflow: visible;isolation: isolate}.zpShowcaseWhite__switch--mobile,.zpShowcaseWhite__switch--floating{display: none}.zpShowcaseWhite__switchBtn{min-height: 44px;min-width: 0;position: relative;z-index: 2;border: 0 !important;background: transparent !important;color: rgba(5,5,5,.58) !important;display: inline-flex;align-items: center;justify-content: center;padding: 0 16px;border-radius: 999px;cursor: pointer;appearance: none;-webkit-appearance: none;font-size: 20px;line-height: 1;font-weight: 720;letter-spacing: -0.012em;transition: color .24s ease,transform .24s cubic-bezier(.16,1,.3,1);white-space: nowrap}.zpShowcaseWhite__switchBtn span{position: relative;z-index: 3;display: inline-block;color: inherit;transition: transform .28s cubic-bezier(.16,1,.3,1)}.zpShowcaseWhite__switchBtn.is-active{color: #ffffff !important}.zpShowcaseWhite__switchBtn.is-active span{transform: translateY(-.5px)}.zpShowcaseWhite__switchThumb{position: absolute;z-index: 1;top: 4px;left: 4px;width: calc(50% - 4px);height: calc(100% - 8px);border-radius: 999px;background: var(--zp-btn-grad);box-shadow: 0 12px 28px rgba(7,20,38,.18);transform: translateX(0);transition: transform .36s cubic-bezier(.16,1,.3,1)}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__switchThumb{transform: translateX(100%)}.zpShowcaseWhite__tapHint{position: absolute;z-index: 5;right: 20px;top: -22px;width: 38px;height: 38px;display: grid;place-items: center;pointer-events: none;opacity: .96;filter: drop-shadow(0 12px 18px rgba(7,20,38,.16));animation: zpShowcaseTapWrap 3.8s cubic-bezier(.16,1,.3,1) infinite}.zpShowcaseWhite__tapHand{display: block;font-size: 28px;line-height: 1;transform-origin: 50% 90%;animation: zpShowcaseTapHand 3.8s cubic-bezier(.16,1,.3,1) infinite}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__tapHint{right: auto;left: 20px;animation-name: zpShowcaseTapWrapBack}@keyframes zpShowcaseTapWrap{0%,12%,100%{transform: translate3d(0,0,0);opacity: .58}22%{transform: translate3d(-8px,3px,0);opacity: 1}30%{transform: translate3d(-8px,3px,0) scale(.92);opacity: 1}40%{transform: translate3d(-8px,3px,0) scale(1);opacity: 1}58%{transform: translate3d(-112px,3px,0);opacity: 1}74%{transform: translate3d(-112px,3px,0);opacity: .72}}@keyframes zpShowcaseTapWrapBack{0%,12%,100%{transform: translate3d(0,0,0);opacity: .58}22%{transform: translate3d(8px,3px,0);opacity: 1}30%{transform: translate3d(8px,3px,0) scale(.92);opacity: 1}40%{transform: translate3d(8px,3px,0) scale(1);opacity: 1}58%{transform: translate3d(112px,3px,0);opacity: 1}74%{transform: translate3d(112px,3px,0);opacity: .72}}@keyframes zpShowcaseTapHand{0%,18%,100%{transform: rotate(-12deg) scale(1)}28%{transform: rotate(-12deg) scale(.88)}38%{transform: rotate(-12deg) scale(1)}58%{transform: rotate(8deg) scale(1)}}.zpShowcaseWhite__progress,.zpShowcaseWhite__floatProgressLine{display: grid;grid-template-columns: auto minmax(100px,1fr) auto;gap: 12px;align-items: center;color: var(--zp-black);font-size: 13px;line-height: 1;font-weight: 700;letter-spacing: .06em}.zpShowcaseWhite__progress{margin-bottom: 14px}.zpShowcaseWhite__progress i,.zpShowcaseWhite__floatProgressLine i{height: 2px;border-radius: 999px;background: rgba(5,5,5,.10);position: relative;overflow: hidden}.zpShowcaseWhite__progress b,.zpShowcaseWhite__floatProgressLine b{position: absolute;inset: 0 auto 0 0;width: 0%;border-radius: inherit;background: var(--zp-grad);transition: width .18s linear}.zpShowcaseWhite__side p{margin: 0;color: rgba(5,5,5,.52);font-size: 13.5px;line-height: 1.55;letter-spacing: -0.004em;font-weight: 400}.zpShowcaseWhite__floatingProgress{position: absolute;z-index: 50;left: 50%;top: clamp(118px,8vw,152px);width: min(430px,calc(100% - 40px));padding: 12px;border-radius: 28px;background: rgba(255,255,255,.91);border: 1px solid rgba(5,5,5,.06);backdrop-filter:none;-webkit-backdrop-filter:none;box-shadow: 0 20px 62px rgba(7,20,38,.10);opacity: 0;pointer-events: none;transform: translate3d(-50%,14px,0) scale(.985);transition: opacity .38s cubic-bezier(.16,1,.3,1),transform .44s cubic-bezier(.16,1,.3,1)}.zpShowcaseWhite__floatProgressLine{width: 100%;padding: 4px 6px 11px}.zpShowcaseWhite__switch--floating{display: grid;width: 100%;margin: 0;min-height: 46px;opacity: 0;transform: translate3d(0,9px,0) scale(.985);pointer-events: none;transition: opacity .36s cubic-bezier(.16,1,.3,1) .06s,transform .42s cubic-bezier(.16,1,.3,1) .06s}.zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchBtn{min-height: 46px;font-size: 19.5px;padding: 0 14px}.zpShowcaseWhite.is-flowing .zpShowcaseWhite__floatingProgress{opacity: 1;pointer-events: auto;transform: translate3d(-50%,0,0) scale(1)}.zpShowcaseWhite.is-flowing .zpShowcaseWhite__switch--floating{opacity: 1;transform: translate3d(0,0,0) scale(1);pointer-events: auto}.zpShowcaseWhite__stage{position: relative;width: 100vw;max-width: 100vw;margin-left: 0;overflow: visible;padding: 0;transition: margin .28s cubic-bezier(.16,1,.3,1),opacity .26s ease,transform .34s cubic-bezier(.16,1,.3,1);transform: translateZ(0);backface-visibility: hidden}.zpShowcaseWhite.is-switching .zpShowcaseWhite__stage{opacity: .05;transform: translate3d(0,12px,0)}.zpShowcaseWhite__rail{display: flex;align-items: center;gap: clamp(22px,2vw,34px);width: max-content;will-change: transform;transform: translate3d(0,0,0);transition: none;padding: 2px clamp(16px,2.8vw,44px) 10px 0;backface-visibility: hidden;contain: layout paint style}.zpShowcaseWhite__card{--shift: 0px;flex: 0 0 clamp(620px,58vw,940px);width: clamp(620px,58vw,940px);aspect-ratio: 2560 / 1707;min-height: 0;max-height: min(64vh,640px);border-radius: clamp(22px,1.65vw,30px);background: #05070b;color: #ffffff;position: relative;overflow: hidden;isolation: isolate;cursor: pointer;contain: layout paint style;will-change: transform,opacity;backface-visibility: hidden;border: 0 !important;outline: 0 !important;box-shadow: none !important;clip-path: inset(0 round clamp(22px,1.65vw,30px));-webkit-mask-image: -webkit-radial-gradient(white,black);transform: translate3d(var(--shift),34px,0) scale(.972);opacity: 0;transition: opacity .7s cubic-bezier(.16,1,.3,1),transform .84s cubic-bezier(.16,1,.3,1),filter .32s ease}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{flex: 0 0 clamp(468px,44.2vw,728px);width: clamp(468px,44.2vw,728px);aspect-ratio: 1 / 1;max-height: min(63.7vh,728px);border-radius: clamp(22px,1.65vw,30px)}.zpShowcaseWhite.is-visible .zpShowcaseWhite__card{opacity: 1;transform: translate3d(var(--shift),0,0) scale(1)}.zpShowcaseWhite__card.is-active{z-index: 20}.zpShowcaseWhite__card.is-past{filter: saturate(.94) brightness(.96)}.zpShowcaseWhite__card:hover{z-index: 30}.zpShowcaseWhite.is-mobile-dragging .zpShowcaseWhite__card a,.zpShowcaseWhite.is-mobile-dragging .zpShowcaseWhite__card button{pointer-events: none !important}.zpShowcaseWhite__media{position: absolute;inset: -1px;z-index: 1;background: #05070b;overflow: hidden;border-radius: inherit}.zpShowcaseWhite__media img{width: 100%;height: 100%;display: block;object-fit: cover;object-position: center center;filter: saturate(1) brightness(.98);transform: scale(1) translateZ(0);backface-visibility: hidden;transition: transform .72s cubic-bezier(.16,1,.3,1),filter .24s ease}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__media img{object-fit: cover;transform: scale(1) translateZ(0)}.zpShowcaseWhite__card:hover .zpShowcaseWhite__media img{transform: scale(1.026) translateZ(0);filter: saturate(1.04) brightness(1)}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card:hover .zpShowcaseWhite__media img{transform: scale(1.022) translateZ(0)}.zpShowcaseWhite__veil{position: absolute;inset: 0;z-index: 2;pointer-events: none;border-radius: inherit;background: linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0) 34%,rgba(0,0,0,.12) 52%,rgba(0,0,0,.62) 78%,rgba(0,0,0,.93) 100%)}.zpShowcaseWhite__veil::after{content: "";position: absolute;left: 0;right: 0;bottom: 0;height: 48%;background: linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,.76) 58%,rgba(0,0,0,.98) 100%)}.zpShowcaseWhite__top{position: relative;z-index: 4;padding: clamp(22px,2vw,30px);display: flex;align-items: flex-start;justify-content: flex-end;gap: 18px;pointer-events: none}.zpShowcaseWhite__pill{display: inline-flex;align-items: center;min-height: 30px;padding: 0 12px;border-radius: 999px;background: rgba(255,255,255,.18);color: #ffffff;backdrop-filter:none;-webkit-backdrop-filter:none;font-size: 10px;line-height: 1;letter-spacing: .11em;text-transform: uppercase;font-weight: 760;white-space: nowrap;border: 0 !important;outline: 0 !important}.zpShowcaseWhite__index{color: rgba(255,255,255,.72);font-size: 12px;line-height: 1;letter-spacing: .12em;font-weight: 760}.zpShowcaseWhite__content{position: absolute;z-index: 5;left: clamp(24px,2.2vw,34px);right: clamp(24px,2.2vw,34px);bottom: clamp(24px,2.2vw,34px);pointer-events: none}.zpShowcaseWhite__name{margin: 0;color: #ffffff;font-size: clamp(31px,2.65vw,48px);line-height: .96;letter-spacing: -0.052em;font-weight: 560;font-variation-settings: "wght" 560;max-width: 760px;text-shadow: 0 14px 42px rgba(0,0,0,.66)}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__name{font-weight: 660;font-size: clamp(27px,2.2vw,38px)}.zpShowcaseWhite__sub{display: block;margin-top: 8px;color: rgba(255,255,255,.74);font-size: clamp(16px,1.24vw,21px);line-height: 1.12;letter-spacing: -0.032em;font-weight: 440;font-variation-settings: "wght" 440;max-width: 760px}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__sub{font-size: clamp(14px,1.05vw,18px)}.zpShowcaseWhite__meta{display: flex;flex-wrap: wrap;gap: 7px;margin-top: 16px}.zpShowcaseWhite__meta span{min-height: 28px;display: inline-flex;align-items: center;padding: 0 10px;border-radius: 999px;background: rgba(255,255,255,.12);color: rgba(255,255,255,.88);font-size: 10px;line-height: 1;letter-spacing: .07em;text-transform: lowercase;font-weight: 650;backdrop-filter:none;-webkit-backdrop-filter:none}.zpShowcaseWhite__actions{display: flex;flex-wrap: wrap;gap: 10px;margin-top: 20px;pointer-events: auto}.zpShowcaseWhite__btn,.zpShowcaseWhite__ctaBtn,.zpShowcaseDarkCta__btn,.zpShowcasePop__cta a{--zpBtnBase: #ffffff;--zpBtnFill: linear-gradient(90deg,#05070b 0%,#0b1830 42%,#102a4f 72%,#1c477a 100%);min-height: 52px;display: inline-flex;align-items: center;justify-content: center;gap: 10px;padding: 0 24px;border-radius: 999px;background: var(--zpBtnFill) left center / 0% 100% no-repeat,linear-gradient(var(--zpBtnBase),var(--zpBtnBase)) right center / 100% 100% no-repeat !important;color: #111111 !important;font-family: var(--zp-font);font-size: 15px;line-height: 1;font-weight: 720;letter-spacing: -0.01em;border: 1px solid #ffffff !important;position: relative;overflow: hidden;isolation: isolate;z-index: 1;white-space: nowrap;cursor: pointer;text-decoration: none;appearance: none;-webkit-appearance: none;transition: background-size .34s cubic-bezier(.16,1,.3,1),color .22s ease,border-color .22s ease,transform .22s ease,filter .22s ease !important}.zpShowcaseWhite__btn::before,.zpShowcaseWhite__ctaBtn::before,.zpShowcaseDarkCta__btn::before,.zpShowcasePop__cta a::before{content: "";position: absolute;inset: -1px;z-index: -1;border-radius: inherit;background: var(--zpBtnFill);transform: scaleX(0);transform-origin: left center;transition: transform .34s cubic-bezier(.16,1,.3,1);pointer-events: none}.zpShowcaseWhite__btn > *,.zpShowcaseWhite__ctaBtn > *,.zpShowcaseDarkCta__btn > *,.zpShowcasePop__cta a > *{position: relative;z-index: 2}.zpShowcaseWhite__btn{min-height: 47px;padding: 0 19px;font-size: 13px}.zpShowcaseWhite__btn:hover,.zpShowcaseWhite__ctaBtn:hover,.zpShowcaseDarkCta__btn:hover,.zpShowcasePop__cta a:hover{background-size: 100% 100%,0% 100% !important;color: #ffffff !important;border-color: rgba(28,71,122,.58) !important;transform: translateY(-2px)}.zpShowcaseWhite__btn svg,.zpShowcaseWhite__ctaBtn svg,.zpShowcaseDarkCta__btn svg,.zpShowcasePop__cta a svg{width: 16px;height: 16px;stroke: currentColor;fill: none;stroke-width: 2;stroke-linecap: round;stroke-linejoin: round;transition: transform .22s cubic-bezier(.16,1,.3,1) !important}.zpShowcaseWhite__btn:hover::before,.zpShowcaseWhite__ctaBtn:hover::before,.zpShowcaseDarkCta__btn:hover::before,.zpShowcasePop__cta a:hover::before{transform: scaleX(1)}.zpShowcaseWhite__btn:hover svg,.zpShowcaseWhite__ctaBtn:hover svg,.zpShowcaseDarkCta__btn:hover svg,.zpShowcasePop__cta a:hover svg{transform: translate(3px,-3px)}.zpShowcaseWhite__btn--live,.zpShowcasePop__cta a:nth-child(2),.zpShowcaseDarkCta__btn--ghost{--zpBtnBase: rgba(255,255,255,.10);color: #ffffff !important;border-color: rgba(255,255,255,.28) !important;backdrop-filter:none;-webkit-backdrop-filter:none}.zpShowcaseWhite__btn--live{--zpBtnBase: rgba(255,255,255,.14)}.zpShowcaseWhite__bottom{display: flex;align-items: center;justify-content: space-between;gap: 18px;margin-top: clamp(20px,2.2vw,30px);opacity: 1;transform: translate3d(0,0,0);transition: opacity .34s ease,transform .34s ease}.zpShowcaseWhite.is-flowing .zpShowcaseWhite__bottom{opacity: .18;transform: translate3d(0,16px,0);pointer-events: none}.zpShowcaseWhite__hint{display: inline-flex;align-items: center;gap: 12px;color: rgba(5,5,5,.48);font-size: 12px;line-height: 1;letter-spacing: .16em;text-transform: uppercase;font-weight: 680}.zpShowcaseWhite__hint i{width: 112px;height: 1px;background: rgba(5,5,5,.14);position: relative;overflow: hidden}.zpShowcaseWhite__hint i::after{content: "";position: absolute;inset: 0 auto 0 0;width: 36%;background: var(--zp-grad);animation: zpShowcaseHint 1.6s ease-in-out infinite}@keyframes zpShowcaseHint{0%{transform: translateX(-120%)}100%{transform: translateX(330%)}}.zpShowcaseWhite__controls{display: flex;align-items: center;gap: 10px}.zpShowcaseWhite__navBtn{width: 48px;height: 48px;border-radius: 999px;border: 0 !important;outline: 0 !important;background: #f2f3f5;color: #050505;display: grid;place-items: center;cursor: pointer;appearance: none;-webkit-appearance: none;transition: background .2s ease,color .2s ease,transform .2s ease}.zpShowcaseWhite__navBtn:hover{background: #071426;color: #ffffff;transform: translateY(-2px)}.zpShowcaseWhite__navBtn svg{width: 20px;height: 20px;stroke: currentColor;fill: none;stroke-width: 2;stroke-linecap: round;stroke-linejoin: round}.zpShowcaseWhite__card--cta{background: radial-gradient(circle at 12% 0%,rgba(28,71,122,.22),transparent 34%),radial-gradient(circle at 86% 14%,rgba(59,110,168,.13),transparent 32%),linear-gradient(135deg,#030407 0%,#05070b 48%,#07101d 100%);border: 0 !important;cursor: default}.zpShowcaseWhite__card--cta::before,.zpShowcaseDarkCta__bleed::before{content: "";position: absolute;inset: 0;z-index: 1;opacity: .052;background-image: linear-gradient(rgba(255,255,255,.13) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.13) 1px,transparent 1px);background-size: 54px 54px;pointer-events: none}.zpShowcaseWhite__ctaFrame,.zpShowcaseDarkCta__frame{--shine-size: 150px;--shine-speed: 4.8s}.zpShowcaseWhite__ctaFrame{position: absolute;z-index: 3;inset: clamp(24px,3vw,48px);border-radius: clamp(20px,1.6vw,30px);display: grid;place-items: center;text-align: center;padding: clamp(28px,4vw,58px);overflow: hidden;isolation: isolate;background: rgba(255,255,255,.012);border: 1px solid rgba(255,255,255,.30)}.zpShowcaseWhite__ctaFrame::before,.zpShowcaseDarkCta__frame::before{content: "";position: absolute;inset: -1px;z-index: -1;border-radius: inherit;padding: 1px;background: linear-gradient(90deg,transparent 0%,rgba(255,255,255,0) 35%,rgba(59,110,168,.98) 50%,rgba(255,255,255,.80) 56%,rgba(59,110,168,.98) 62%,rgba(255,255,255,0) 78%,transparent 100%);background-size: 260px 100%;background-repeat: no-repeat;animation: zpShowcaseBorderShine var(--shine-speed) linear infinite;-webkit-mask: linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite: xor;mask-composite: exclude;opacity: .9}.zpShowcaseWhite__ctaFrame::after,.zpShowcaseDarkCta__frame::after{content: "";position: absolute;inset: auto 22px 22px 22px;height: 3px;border-radius: 999px;background: linear-gradient(90deg,var(--zp-accent),var(--zp-accent2),var(--zp-accent3));background-size: 220% 100%;animation: zpShowcaseLineRun 2.8s linear infinite;opacity: .84}@keyframes zpShowcaseBorderShine{0%{background-position: -260px 0}100%{background-position: calc(100% + 260px) 0}}@keyframes zpShowcaseLineRun{0%{background-position: 0% 50%}100%{background-position: 220% 50%}}.zpShowcaseWhite__ctaContent{position: relative;z-index: 4;max-width: 620px}.zpShowcaseWhite__ctaEyebrow{color: rgba(255,255,255,.72);margin-bottom: 18px}.zpShowcaseWhite__ctaTitle{margin: 0;color: #ffffff;font-size: clamp(34px,4.2vw,64px);line-height: .96;letter-spacing: -0.052em;font-weight: 560;font-variation-settings: "wght" 560;text-wrap: balance}.zpShowcaseWhite__ctaText{max-width: 540px;margin: 18px auto 0;color: rgba(255,255,255,.68);font-size: clamp(13px,1vw,15px);line-height: 1.62;letter-spacing: -0.006em;font-weight: 400}.zpShowcaseWhite__ctaActions{display: flex;flex-wrap: wrap;justify-content: center;gap: 10px;margin-top: 24px}.zpShowcaseDarkCta{--zp-accent: #102a4f;--zp-accent2: #1c477a;--zp-accent3: #3b6ea8;--zp-dark: #05070b;--zp-line: rgba(255,255,255,.105);--zp-muted: rgba(255,255,255,.68);--zp-btn-grad: linear-gradient(90deg,#05070b 0%,#0b1830 42%,#102a4f 72%,#1c477a 100%);--zp-font: "Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;width: 100%;max-width: 100%;margin: 0;padding: clamp(70px,7vw,118px) 0;position: relative;isolation: isolate;overflow: hidden;background: transparent;color: #ffffff;font-family: var(--zp-font);-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;text-rendering: geometricPrecision;font-synthesis: none;transform: translateZ(0)}.zpShowcaseDarkCta__bleed{position: absolute;z-index: -4;top: 0;bottom: 0;left: 50%;width: 100vw;max-width: 100vw;transform: translateX(-50%);pointer-events: none;overflow: hidden;background: radial-gradient(circle at 12% 0%,rgba(28,71,122,.22),transparent 34%),radial-gradient(circle at 86% 14%,rgba(59,110,168,.13),transparent 32%),radial-gradient(circle at 50% 110%,rgba(16,42,79,.14),transparent 40%),linear-gradient(135deg,#030407 0%,#05070b 42%,#07101d 100%)}.zpShowcaseDarkCta__bleed::before{mask-image: linear-gradient(180deg,transparent 0%,#000 18%,#000 78%,transparent 100%);-webkit-mask-image: linear-gradient(180deg,transparent 0%,#000 18%,#000 78%,transparent 100%)}.zpShowcaseDarkCta__bleed::after{content: "";position: absolute;inset: 0;z-index: 2;background: linear-gradient(90deg,rgba(2,3,6,.96) 0%,rgba(5,9,17,.78) 42%,rgba(2,3,6,.96) 100%),linear-gradient(180deg,rgba(2,3,6,.84) 0%,rgba(6,13,25,.52) 48%,rgba(2,3,6,.96) 100%),radial-gradient(circle at 18% 10%,rgba(28,71,122,.18),transparent 36%),radial-gradient(circle at 78% 18%,rgba(59,110,168,.10),transparent 34%)}.zpShowcaseDarkCta::before{content: "";position: absolute;z-index: -2;left: 50%;top: 8%;width: 100vw;max-width: 100vw;height: 58vw;max-height: 760px;transform: translateX(-50%);pointer-events: none;opacity: .10;background: radial-gradient(circle at 50% 35%,rgba(59,110,168,.30),transparent 40%),radial-gradient(circle at 18% 70%,rgba(16,42,79,.30),transparent 36%);filter:none;animation: zpShowcaseDarkGlow 10s ease-in-out infinite}@keyframes zpShowcaseDarkGlow{0%,100%{opacity: .075;transform: translateX(-50%) scale(1)}50%{opacity: .14;transform: translateX(-50%) scale(1.035)}}.zpShowcaseDarkCta__mark{position: absolute;z-index: 1;left: 50%;bottom: -0.18em;transform: translateX(-50%);width: max-content;color: #ffffff;opacity: .060;font-size: clamp(94px,15vw,250px);line-height: .78;letter-spacing: -.085em;font-weight: 760;pointer-events: none;user-select: none;white-space: nowrap;mix-blend-mode: screen;font-family: var(--zp-font);mask-image: linear-gradient(180deg,transparent 0%,#000 28%,#000 54%,transparent 100%);-webkit-mask-image: linear-gradient(180deg,transparent 0%,#000 28%,#000 54%,transparent 100%)}.zpShowcaseDarkCta__inner{width: min(1500px,calc(100% - 56px));max-width: 100%;margin: 0 auto;position: relative;z-index: 3;display: grid;grid-template-columns: minmax(0,1fr) minmax(360px,500px);gap: clamp(30px,6vw,90px);align-items: center}.zpShowcaseDarkCta__eyebrow{color: rgba(255,255,255,.72)}.zpShowcaseDarkCta h2{max-width: 880px;margin: 0;color: #ffffff;font-size: clamp(38px,5.2vw,78px);line-height: .94;letter-spacing: -0.06em;font-weight: 560;font-variation-settings: "wght" 560;text-shadow: none !important;text-wrap: balance}.zpShowcaseDarkCta p{max-width: 650px;margin: 22px 0 0;color: var(--zp-muted);font-size: clamp(13px,1vw,15px);line-height: 1.72;letter-spacing: -0.006em;font-weight: 400;font-variation-settings: "wght" 400}.zpShowcaseDarkCta__panel{display: grid;gap: 18px}.zpShowcaseDarkCta__frame{min-height: 320px;border-radius: 30px;display: grid;place-items: center;text-align: center;padding: 28px;background: rgba(255,255,255,.015);position: relative;overflow: hidden;isolation: isolate;border: 1px solid rgba(255,255,255,.30)}.zpShowcaseDarkCta__frame span{color: #ffffff;font-size: clamp(22px,2.2vw,34px);line-height: 1.04;letter-spacing: -0.045em;font-weight: 560;font-variation-settings: "wght" 560}.zpShowcaseDarkCta__frame span{position: relative;z-index: 3;text-shadow: 0 18px 44px rgba(0,0,0,.44)}.zpShowcaseDarkCta__actions{display: flex;flex-wrap: wrap;gap: 10px}.zpShowcasePop{position: fixed;inset: 0;z-index: 999999;display: flex;align-items: center;justify-content: center;opacity: 0;pointer-events: none;transition: opacity .24s ease;font-family: var(--zp-font,"Plus Jakarta Sans Local",system-ui,sans-serif)}.zpShowcasePop.is-open{opacity: 1;pointer-events: auto}.zpShowcasePop__backdrop{position: absolute;inset: 0;background: rgba(255,255,255,.78);backdrop-filter:none;-webkit-backdrop-filter:none}.zpShowcasePop__panel{position: relative;z-index: 2;width: min(1240px,calc(100vw - 36px));max-height: min(860px,calc(100dvh - 36px));border-radius: 34px;background: #050505;color: #ffffff;overflow: hidden;border: 0 !important;outline: 0 !important;box-shadow: none !important;transform: translateY(18px) scale(.98);transition: transform .32s cubic-bezier(.16,1,.3,1)}.zpShowcasePop.is-open .zpShowcasePop__panel{transform: translateY(0) scale(1)}.zpShowcasePop__close{position: absolute;top: 18px;right: 18px;z-index: 20;width: 46px;height: 46px;border-radius: 999px;border: 0 !important;outline: 0 !important;background: rgba(0,0,0,.45);color: #ffffff;display: grid;place-items: center;cursor: pointer;appearance: none;-webkit-appearance: none;backdrop-filter:none;-webkit-backdrop-filter:none}.zpShowcasePop__close svg{width: 20px;height: 20px;stroke: currentColor;fill: none;stroke-width: 2;stroke-linecap: round;stroke-linejoin: round}.zpShowcasePop__body{max-height: min(860px,calc(100dvh - 36px));overflow-y: auto;-webkit-overflow-scrolling: touch}.zpShowcasePop__layout{display: grid;grid-template-columns: minmax(0,1.04fr) minmax(390px,.96fr);min-height: 720px}.zpShowcasePop__visual{position: relative;min-height: 720px;overflow: hidden;background: #080808}.zpShowcasePop__visual::before{content: "";position: absolute;inset: -30px;background-image: var(--pop-img);background-size: cover;background-position: center top;opacity: .20;filter:none;transform: scale(1.08)}.zpShowcasePop__visualInner{position: absolute;inset: 34px;z-index: 2;display: grid;place-items: center}.zpShowcasePop__visualInner img{width: 100%;height: 100%;display: block;object-fit: cover;object-position: center center;border-radius: 24px;border: 0 !important;outline: 0 !important;background: #05070b}.zpShowcasePop__info{padding: 52px 44px 42px;min-width: 0}.zpShowcasePop__top{display: flex;flex-wrap: wrap;gap: 8px;margin-bottom: 22px;padding-right: 56px}.zpShowcasePop__pill{min-height: 30px;display: inline-flex;align-items: center;padding: 0 12px;border-radius: 999px;background: rgba(255,255,255,.08);color: rgba(255,255,255,.88);font-size: 10px;line-height: 1;letter-spacing: .1em;text-transform: uppercase;font-weight: 760}.zpShowcasePop__title{margin: 0;color: #ffffff;font-size: clamp(38px,4.2vw,62px);line-height: .92;letter-spacing: -0.06em;font-weight: 560}.zpShowcasePop__subtitle{margin: 12px 0 24px;color: rgba(255,255,255,.62);font-size: 20px;line-height: 1.28;letter-spacing: -0.02em}.zpShowcasePop__text{margin: 0 0 28px;color: rgba(255,255,255,.76);font-size: 15.5px;line-height: 1.72}.zpShowcasePop__grid{display: grid;grid-template-columns: 1fr 1fr;border-radius: 18px;overflow: hidden;margin-bottom: 26px;background: rgba(255,255,255,.045)}.zpShowcasePop__gridItem{padding: 16px 18px}.zpShowcasePop__label,.zpShowcasePop__scopeTitle{margin: 0 0 8px;color: rgba(255,255,255,.46);font-size: 11px;line-height: 1;letter-spacing: .08em;text-transform: uppercase;font-weight: 760}.zpShowcasePop__value{color: rgba(255,255,255,.88);font-size: 14px;line-height: 1.45}.zpShowcasePop__scope{display: grid;grid-template-columns: 1fr 1fr;gap: 8px 16px;list-style: none;margin: 0 0 28px;padding: 0}.zpShowcasePop__scope li{display: flex;gap: 10px;color: rgba(255,255,255,.76);font-size: 14px;line-height: 1.45}.zpShowcasePop__scope li::before{content: "";width: 7px;height: 7px;flex: 0 0 7px;border-radius: 999px;margin-top: .48em;background: linear-gradient(90deg,#102a4f,#1c477a,#3b6ea8)}.zpShowcasePop__tools{display: flex;flex-wrap: wrap;gap: 8px;margin: -8px 0 28px}.zpShowcasePop__tool{min-height: 34px;display: inline-flex;align-items: center;padding: 0 12px;border-radius: 999px;background: rgba(255,255,255,.07);color: rgba(255,255,255,.82);font-size: 12px;line-height: 1;font-weight: 560}.zpShowcasePop__cta{display: flex;flex-wrap: wrap;gap: 10px}html.zpShowcaseNoScroll,body.zpShowcaseNoScroll{overflow: hidden !important}@media (max-width: 1180px){.zpShowcaseWhite__head,.zpShowcaseWhite__bottom{width: min(1500px,calc(100% - 32px))}.zpShowcaseWhite__inner{padding-top: 48px}.zpShowcaseWhite__head{grid-template-columns: 1fr;gap: 18px;margin-bottom: 30px}.zpShowcaseWhite__side{max-width: 560px}.zpShowcaseWhite__card{flex: 0 0 78vw;width: 78vw;aspect-ratio: 2560 / 1707;max-height: min(58vh,560px)}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{flex: 0 0 min(75.4vw,702px);width: min(75.4vw,702px);aspect-ratio: 1 / 1;max-height: min(62.4vh,702px)}.zpShowcaseDarkCta__inner{width: min(1500px,calc(100% - 32px));grid-template-columns: 1fr}.zpShowcaseDarkCta__panel{max-width: 620px}.zpShowcasePop__layout{grid-template-columns: 1fr;min-height: auto}.zpShowcasePop__visual{min-height: 420px}.zpShowcasePop__visualInner{position: relative;inset: auto;padding: 22px;height: 420px}}@media (min-width: 761px){.zpShowcaseWhite:not([data-mode="logo"]) .zpShowcaseWhite__media img{object-fit: contain !important;object-position: center center !important;transform: scale(1) translateZ(0) !important;background: #05070b}.zpShowcaseWhite:not([data-mode="logo"]) .zpShowcaseWhite__card:hover .zpShowcaseWhite__media img{transform: scale(1.014) translateZ(0) !important;filter: saturate(1.04) brightness(1)}}@media (max-width: 760px){.zpShowcaseWhite{overflow: hidden;overflow-anchor: none}.zpShowcaseWhite__spacer{min-height: auto !important;height: auto !important;position: relative;overflow-anchor: none}.zpShowcaseWhite__sticky{position: relative !important;top: auto !important;height: auto !important;min-height: auto !important;overflow: visible !important;contain: none !important;display: block;align-items: stretch;overflow-anchor: none}.zpShowcaseWhite__inner{width: 100%;padding: 58px 0 54px !important;overflow: visible;overflow-anchor: none}.zpShowcaseWhite__head,.zpShowcaseWhite__bottom{width: calc(100% - 20px)}.zpShowcaseWhite__head{grid-template-columns: 1fr;gap: 16px;max-height: none !important;overflow: visible !important;opacity: 1 !important;transform: none !important;pointer-events: auto !important;margin-bottom: 28px !important}.zpShowcaseWhite.is-flowing .zpShowcaseWhite__head{max-height: none !important;opacity: 1 !important;transform: none !important;pointer-events: auto !important;margin-bottom: 28px !important}.zpShowcaseWhite__floatingProgress{display: none !important}.zpShowcaseWhite__stage{width: 100vw !important;max-width: 100vw !important;margin-left: calc(50% - 50vw) !important;margin-top: 0 !important;overflow-x: auto !important;overflow-y: hidden !important;-webkit-overflow-scrolling: touch;scroll-snap-type: none !important;scroll-padding-left: 6vw !important;overscroll-behavior-x: contain;overscroll-behavior-y: auto;overscroll-behavior-inline: contain;touch-action: pan-x pan-y !important;scroll-behavior: auto !important;overflow-anchor: none;min-height: min(132vw,680px);contain: layout paint;transform: none !important;opacity: 1 !important;scrollbar-width: none;padding: 2px 0 16px}.zpShowcaseWhite__stage::-webkit-scrollbar{display: none}.zpShowcaseWhite.is-flowing .zpShowcaseWhite__stage,.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__stage,.zpShowcaseWhite[data-mode="logo"].is-flowing .zpShowcaseWhite__stage{margin-top: 0 !important}.zpShowcaseWhite__rail{width: max-content !important;transform: none !important;will-change: auto !important;padding: 0 6vw 0 6vw !important;gap: 14px !important;align-items: flex-start !important;overflow-anchor: none;min-height: min(132vw,680px)}.zpShowcaseWhite__card{flex: 0 0 88vw !important;width: 88vw !important;aspect-ratio: 9 / 13.25 !important;max-height: none !important;height: auto !important;min-height: 0 !important;scroll-snap-align: none !important;scroll-snap-stop: normal !important;opacity: 1 !important;transform: none !important;border-radius: 22px;clip-path: inset(0 round 22px);transition: none !important;overflow-anchor: none;-webkit-user-select: none;user-select: none}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{flex: 0 0 88vw !important;width: 88vw !important;aspect-ratio: 1 / 1 !important;max-height: none !important}.zpShowcaseWhite__media{background: #05070b}.zpShowcaseWhite__media img,.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__media img,.zpShowcaseWhite:not([data-mode="logo"]) .zpShowcaseWhite__media img{object-fit: cover !important;object-position: center center !important;transform: scale(1) translateZ(0) !important;filter: saturate(1) brightness(.94)}.zpShowcaseWhite__card:hover .zpShowcaseWhite__media img,.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card:hover .zpShowcaseWhite__media img{transform: scale(1.018) translateZ(0) !important;filter: saturate(1.04) brightness(1)}.zpShowcaseWhite__veil{background: linear-gradient(180deg,rgba(0,0,0,.02) 0%,rgba(0,0,0,.06) 42%,rgba(0,0,0,.42) 68%,rgba(0,0,0,.92) 100%)}.zpShowcaseWhite__top{padding: 18px}.zpShowcaseWhite__pill{min-height: 28px;font-size: 9px;letter-spacing: .09em;max-width: 78%;overflow: hidden;text-overflow: ellipsis}.zpShowcaseWhite__content{left: 20px;right: 20px;bottom: 22px}.zpShowcaseWhite__name{font-size: 31px;line-height: .98;letter-spacing: -0.046em}.zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__name{font-size: 27px}.zpShowcaseWhite__sub{margin-top: 7px;font-size: 15px;line-height: 1.14;letter-spacing: -0.026em;color: rgba(255,255,255,.78);text-transform: lowercase}.zpShowcaseWhite__meta{display: none !important}.zpShowcaseWhite__actions{display: grid;grid-template-columns: 1fr;margin-top: 18px;gap: 12px;padding: 0 3px}.zpShowcaseWhite__btn{width: 100%;min-height: 52px;padding: 0 22px;font-size: 14px;border-radius: 999px;touch-action: manipulation}.zpShowcaseWhite__switch--desktop{display: none}.zpShowcaseWhite__switch--mobile{display: grid;width: 100%;margin: 20px 0 0}.zpShowcaseWhite__switchBtn{min-width: 0;min-height: 54px;padding: 0 14px;font-size: 19px;letter-spacing: -0.035em}.zpShowcaseWhite__tapHint{top: -18px;right: 18px;transform: scale(.9)}.zpShowcaseWhite__side{display: none}.zpShowcaseWhite__bottom{width: calc(100% - 20px) !important;margin-top: 18px !important;display: flex !important;opacity: 1 !important;transform: none !important;pointer-events: auto !important}.zpShowcaseWhite.is-flowing .zpShowcaseWhite__bottom{opacity: 1 !important;transform: none !important;pointer-events: auto !important}.zpShowcaseWhite__hint{gap: 10px;font-size: 10px;letter-spacing: .12em}.zpShowcaseWhite__hint span{font-size: 0}.zpShowcaseWhite__hint span::before{content: "Przesuń karty w prawo";font-size: 10px;letter-spacing: .12em}.zpShowcaseWhite__hint i{width: 92px;background: rgba(5,5,5,.13)}.zpShowcaseWhite__hint i::after{width: 42%;animation: zpShowcaseMobileSwipeLine 1.35s cubic-bezier(.16,1,.3,1) infinite}@keyframes zpShowcaseMobileSwipeLine{0%{transform: translateX(-120%);opacity: .35}45%{opacity: 1}100%{transform: translateX(330%);opacity: .35}}.zpShowcaseWhite__controls{display: flex !important;flex: 0 0 auto}.zpShowcaseWhite__navBtn{width: 42px !important;height: 42px !important}.zpShowcaseWhite__card,.zpShowcaseWhite__media,.zpShowcaseWhite__content{-webkit-tap-highlight-color: transparent}.zpShowcaseWhite__card{overscroll-behavior: auto;touch-action: auto !important}.zpShowcaseWhite__card a,.zpShowcaseWhite__card button{touch-action: manipulation}.zpShowcaseWhite.is-switching .zpShowcaseWhite__stage{opacity: 1 !important;transform: none !important}.zpShowcaseDarkCta{padding: 58px 0 72px}.zpShowcaseDarkCta__inner{width: calc(100% - 20px);grid-template-columns: 1fr;gap: 28px}.zpShowcaseDarkCta h2{font-size: clamp(34px,10vw,48px);line-height: .98;letter-spacing: -0.056em}.zpShowcaseDarkCta p{font-size: 13px;line-height: 1.62}.zpShowcaseDarkCta__frame{min-height: 260px;border-radius: 24px}.zpShowcaseDarkCta__actions{display: grid;grid-template-columns: 1fr}.zpShowcaseDarkCta__btn{width: 100%;min-height: 54px}.zpShowcaseDarkCta__mark{left: 50%;right: auto;bottom: -0.22em;transform: translateX(-50%);font-size: clamp(90px,25vw,170px);opacity: 1;mask-image: linear-gradient(180deg,transparent 0%,#000 34%,#000 54%,transparent 100%);-webkit-mask-image: linear-gradient(180deg,transparent 0%,#000 34%,#000 54%,transparent 100%)}.zpShowcasePop{align-items: stretch !important;justify-content: stretch !important;overflow: hidden !important;touch-action: auto !important;padding: 0}.zpShowcasePop__panel{width: 100vw !important;height: 100dvh !important;max-height: 100dvh !important;border-radius: 0 !important;overflow-y: auto !important;overflow-x: hidden !important;-webkit-overflow-scrolling: touch !important;overscroll-behavior: auto !important;touch-action: pan-y !important}.zpShowcasePop__body{height: auto !important;min-height: 100% !important;max-height: none !important;overflow: visible !important;-webkit-overflow-scrolling: touch !important;padding-bottom: env(safe-area-inset-bottom)}.zpShowcasePop__layout{min-height: auto !important;grid-template-columns: 1fr}.zpShowcasePop__visual{min-height: 300px}.zpShowcasePop__visualInner{height: 300px;padding: 0}.zpShowcasePop__visualInner img{border-radius: 0;object-fit: cover;object-position: center center}.zpShowcasePop__info{padding: 28px 18px calc(46px + env(safe-area-inset-bottom)) !important}.zpShowcasePop__top{padding-right: 52px}.zpShowcasePop__title{font-size: 36px}.zpShowcasePop__subtitle{font-size: 16px}.zpShowcasePop__grid,.zpShowcasePop__scope{grid-template-columns: 1fr}.zpShowcasePop__tools{margin-top: -4px}.zpShowcasePop__cta{display: grid;grid-template-columns: 1fr}.zpShowcasePop__cta a{width: 100%}}@media (prefers-reduced-motion: reduce){.zpShowcaseWhite__sticky{position: relative;min-height: auto;height: auto}.zpShowcaseWhite__spacer{min-height: auto !important}.zpShowcaseWhite__rail{transform: none !important}.zpShowcaseWhite__stage{overflow-x: auto;-webkit-overflow-scrolling: touch}.zpShowcaseWhite__card{opacity: 1 !important;transform: none !important}.zpShowcaseWhite__tapHint,.zpShowcaseWhite__tapHand,.zpShowcaseDarkCta::before,.zpShowcaseWhite__btn,.zpShowcaseWhite__ctaBtn,.zpShowcaseDarkCta__btn,.zpShowcaseWhite__ctaFrame::before,.zpShowcaseWhite__ctaFrame::after,.zpShowcaseDarkCta__frame::before,.zpShowcaseDarkCta__frame::after{animation: none !important;transition: none !important}}@media (max-width: 760px){.zpShowcaseWhite,.zpShowcaseWhite__spacer,.zpShowcaseWhite__sticky,.zpShowcaseWhite__inner,.zpShowcaseWhite__head,.zpShowcaseWhite__stage,.zpShowcaseWhite__rail,.zpShowcaseWhite__card{overflow-anchor: none !important;scroll-margin-top: 0 !important}.zpShowcaseWhite__stage{contain: paint !important}.zpShowcaseWhite__card:hover,.zpShowcaseWhite__card:active,.zpShowcaseWhite__card:focus-within{transform: none !important;filter: none !important}.zpShowcaseWhite__card:hover .zpShowcaseWhite__media img,.zpShowcaseWhite__card:active .zpShowcaseWhite__media img{transform: scale(1) translateZ(0) !important}}html,body{overflow-x: clip}@supports not (overflow-x: clip){html,body{overflow-x: hidden}}.zpShowcaseWhite__switch{padding:12px!important;gap:10px!important;border-radius:42px!important}.zpShowcaseWhite__switchBtn{min-height:72px!important;padding:0 28px!important;font-size:clamp(25px,2.1vw,38px)!important;letter-spacing:-.048em!important;border-radius:34px!important}.zpShowcaseWhite__switchThumb{border-radius:34px!important}.zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchBtn{min-height:52px!important;font-size:17.5px!important;padding:0 18px!important}@media(max-width:760px){.zpShowcaseWhite__switch--mobile{margin:20px 0 0!important;padding:10px!important;gap:8px!important}.zpShowcaseWhite__switchBtn{min-height:58px!important;font-size:24px!important;padding:0 18px!important}}.zpShowcaseWhite__stage,.zpShowcaseWhite__rail{transform:translateZ(0);backface-visibility:hidden;perspective:1000px}.zpShowcaseWhite__rail{will-change:transform!important}.zpShowcaseWhite__card{backface-visibility:hidden;transform:translateZ(0)}.zpShowcaseWhite__switch{max-width:min(820px,100%)!important;margin-left:auto!important;margin-right:auto!important;padding:10px!important;gap:8px!important;border-radius:34px!important}.zpShowcaseWhite__switchBtn{min-height:62px!important;padding:0 22px!important;font-size:clamp(20px,1.42vw,27px)!important;letter-spacing:-.04em!important;border-radius:27px!important}.zpShowcaseWhite__switchBtn span{font-size:inherit!important;line-height:1!important}.zpShowcaseWhite__switchIcon{width:21px!important;height:21px!important;min-width:21px!important;stroke-width:2.15!important;opacity:.84!important;position:relative!important;z-index:3!important}.zpShowcaseWhite__switchBtn.is-active .zpShowcaseWhite__switchIcon{opacity:1!important;color:#fff!important}.zpShowcaseWhite__switchThumb{border-radius:27px!important}.zpShowcaseWhite__switch--floating{max-width:min(720px,100%)!important}.zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchBtn{min-height:48px!important;font-size:16.5px!important;padding:0 16px!important;gap:7px!important}.zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchIcon{width:16px!important;height:16px!important;min-width:16px!important}@media(max-width:760px){.zpShowcaseWhite__switch{padding:9px!important;gap:7px!important;border-radius:30px!important}.zpShowcaseWhite__switchBtn{min-height:54px!important;font-size:18px!important;padding:0 12px!important;gap:7px!important;border-radius:24px!important}.zpShowcaseWhite__switchIcon{width:17px!important;height:17px!important;min-width:17px!important}}html body #zpShowcaseWhite,html body #zpShowcaseWhite *{font-family:"Plus Jakarta Sans Local"!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage,html body #zpShowcaseWhite .zpShowcaseWhite__rail{transform:translate3d(0,0,0);backface-visibility:hidden;contain:layout paint}html body #zpShowcaseWhite .zpShowcaseWhite__rail{will-change:transform;transition:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{transform:translate3d(0,0,0);backface-visibility:hidden}html body #zpShowcaseWhite .zpShowcaseWhite__switch{width:100%!important;max-width:min(760px,100%)!important;padding:8px!important;gap:8px!important;border-radius:32px!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseWhite__side .zpShowcaseWhite__switch{max-width:760px!important;margin:0 0 18px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{min-width:0!important;min-height:56px!important;padding:0 18px!important;gap:9px!important;font-size:clamp(17px,1.06vw,22px)!important;line-height:1!important;letter-spacing:-.035em!important;font-weight:720!important;border-radius:25px!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn span{display:inline-flex!important;align-items:center!important;min-width:0!important;max-width:100%!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important;font-size:inherit!important;line-height:1!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon svg{display:block!important;width:18px!important;height:18px!important;min-width:18px!important;flex:0 0 18px!important;stroke-width:2.1!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn.is-active .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn.is-active .zpShowcaseWhite__switchIcon svg{color:#fff!important;stroke:#fff!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchThumb{border-radius:25px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating{max-width:min(650px,100%)!important;padding:7px!important;border-radius:28px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchBtn{min-height:44px!important;padding:0 13px!important;font-size:15px!important;gap:7px!important;border-radius:21px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchIcon svg{width:15px!important;height:15px!important;min-width:15px!important;flex-basis:15px!important}@media(max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile{margin:18px 0 0!important;max-width:100%!important;padding:8px!important;gap:7px!important;border-radius:28px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{min-height:50px!important;font-size:15.5px!important;padding:0 10px!important;gap:6px!important;border-radius:22px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon svg{width:15px!important;height:15px!important;min-width:15px!important;flex-basis:15px!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage{scroll-snap-type:x proximity!important;scroll-behavior:auto!important;-webkit-overflow-scrolling:touch!important;touch-action:pan-x pan-y!important;overscroll-behavior-x:contain!important}}.zpShowcaseWhite .zpShowcaseWhite__switch,.zpShowcaseWhite__switch{width:min(760px,92vw) !important;min-height:96px !important;padding:10px !important;border-radius:34px !important;overflow:hidden !important}.zpShowcaseWhite .zpShowcaseWhite__switchBtn,.zpShowcaseWhite__switchBtn{flex:1 1 50% !important;min-width:0 !important;width:50% !important;height:76px !important;min-height:76px !important;padding:0 22px !important;font-size:clamp(20px,1.8vw,28px) !important;line-height:1 !important;letter-spacing:-.038em !important;font-weight:650 !important;white-space:nowrap !important;overflow:hidden !important}.zpShowcaseWhite .zpShowcaseWhite__switchBtn span,.zpShowcaseWhite__switchBtn span{display:inline-flex !important;align-items:center !important;justify-content:center !important;gap:10px !important;max-width:100% !important;overflow:hidden !important;text-overflow:clip !important;white-space:nowrap !important;font-size:inherit !important;line-height:1 !important;letter-spacing:inherit !important;font-weight:inherit !important}.zpShowcaseWhite .zpShowcaseWhite__switchIcon,.zpShowcaseWhite__switchIcon{width:22px !important;height:22px !important;flex:0 0 22px !important;stroke-width:2 !important;opacity:.82 !important}.zpShowcaseWhite .zpShowcaseWhite__switchBtn span::before,.zpShowcaseWhite__switchBtn span::before{display:none!important;content:none!important}.zpShowcaseWhite .zpShowcaseWhite__switchThumb,.zpShowcaseWhite__switchThumb{top:10px!important;left:10px!important;width:calc(50% - 10px)!important;height:calc(100% - 20px)!important}@media(max-width:860px){.zpShowcaseWhite .zpShowcaseWhite__switch,.zpShowcaseWhite__switch{width:min(620px,calc(100vw - 32px))!important;min-height:88px!important;padding:9px!important;border-radius:30px!important}.zpShowcaseWhite .zpShowcaseWhite__switchBtn,.zpShowcaseWhite__switchBtn{height:68px!important;min-height:68px!important;padding:0 14px!important;font-size:clamp(17px,5.2vw,22px)!important;letter-spacing:-.035em!important}.zpShowcaseWhite .zpShowcaseWhite__switchIcon,.zpShowcaseWhite__switchIcon{width:18px!important;height:18px!important;flex-basis:18px!important}.zpShowcaseWhite .zpShowcaseWhite__switchThumb,.zpShowcaseWhite__switchThumb{top:9px!important;left:9px!important;width:calc(50% - 9px)!important;height:calc(100% - 18px)!important}}.zpShowcaseWhite .zpShowcaseWhite__rail,.zpShowcaseWhite__rail{transform:translate3d(0,0,0);will-change:transform;backface-visibility:hidden;contain:layout paint style}.zpShowcaseWhite .zpShowcaseWhite__card,.zpShowcaseWhite__card{will-change:transform,opacity;backface-visibility:hidden;transform:translate3d(var(--shift),34px,0) scale(.972)}.zpShowcaseWhite__switch{min-height:64px!important;padding:6px!important;gap:0!important;border-radius:999px!important;background:rgba(245,246,248,.96)!important;border:1px solid rgba(7,20,38,.10)!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.94),0 18px 46px rgba(7,20,38,.075)!important;overflow:hidden!important}.zpShowcaseWhite__switchBtn{min-height:52px!important;height:52px!important;padding:0 14px!important;gap:10px!important;font-size:clamp(13px,.96vw,15px)!important;line-height:1!important;font-weight:760!important;letter-spacing:-.025em!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}.zpShowcaseWhite__switchBtn span{min-width:0!important;max-width:100%!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important}.zpShowcaseWhite__switchIcon,.zpShowcaseWhite__switchBtn svg{width:19px!important;height:19px!important;flex:0 0 19px!important;stroke-width:2.05!important}.zpShowcaseWhite__switchThumb{top:6px!important;left:6px!important;width:calc(50% - 6px)!important;height:calc(100% - 12px)!important;box-shadow:0 13px 30px rgba(7,20,38,.20)!important;background:linear-gradient(100deg,#03070d 0%,#071426 36%,#102a4f 72%,#1c477a 100%)!important}.zpShowcaseWhite__tapHint{display:none!important}.zpShowcaseWhite__floatingProgress{backdrop-filter:none!important;-webkit-backdrop-filter:none!important;background:rgba(255,255,255,.96)!important}.zpShowcaseWhite__card,.zpShowcaseWhite__card *,.zpShowcaseWhite__media img,.zpShowcaseWhite__floatingProgress,.zpShowcaseWhite__head,.zpShowcaseWhite__bottom{will-change:transform,opacity!important}.zpShowcaseWhite__card,.zpShowcaseWhite__media,.zpShowcaseWhite__media img,.zpShowcaseWhite__name,.zpShowcaseWhite__type,.zpShowcaseWhite__meta,.zpShowcaseWhite__actions{filter:none!important}.zpShowcaseWhite.is-switching .zpShowcaseWhite__title,.zpShowcaseWhite.is-switching .zpShowcaseWhite__lead{opacity:.42!important;transform:translate3d(0,6px,0) scale(.995)!important;filter:none!important}.zpShowcaseWhite__progress b,.zpShowcaseWhite__floatProgressLine b{transition:width .28s cubic-bezier(.16,1,.3,1)!important}@media(max-width:760px){.zpShowcaseWhite__switch--mobile{display:grid!important;margin-top:22px!important;margin-bottom:0!important}.zpShowcaseWhite__switch{min-height:58px!important;padding:5px!important}.zpShowcaseWhite__switchBtn{min-height:48px!important;height:48px!important;font-size:13px!important;padding:0 10px!important;gap:8px!important}.zpShowcaseWhite__switchIcon,.zpShowcaseWhite__switchBtn svg{width:17px!important;height:17px!important;flex-basis:17px!important}.zpShowcaseWhite__stage{scroll-snap-type:x mandatory!important;scroll-padding-left:6vw!important;scroll-behavior:smooth!important}.zpShowcaseWhite__card{scroll-snap-align:start!important;scroll-snap-stop:always!important}}@media (min-width:761px){html body #zpShowcaseWhite .zpShowcaseWhite__spacer{position:relative!important;min-height:calc(100vh + 1200px);overflow:visible!important}html body #zpShowcaseWhite .zpShowcaseWhite__sticky{position:sticky!important;top:0!important;height:100vh!important;min-height:720px!important;overflow:hidden!important;contain:layout paint!important;background:#fff!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage{contain:paint!important;scroll-behavior:auto!important;-webkit-overflow-scrolling:auto!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{transform:translate3d(0,0,0);transition:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{backface-visibility:hidden!important;transition:box-shadow .26s ease,border-color .26s ease,opacity .26s ease!important}html body #zpShowcaseWhite .zpShowcaseWhite__card.is-past{filter:none!important}html body #zpShowcaseWhite{--zpShowcaseEdge: max(28px,calc((100vw - 1500px) / 2))}html body #zpShowcaseWhite .zpShowcaseWhite__stage{width:100vw!important;max-width:100vw!important;margin-left:0!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{padding-left:var(--zpShowcaseEdge)!important;padding-right:var(--zpShowcaseEdge)!important;padding-top:2px!important;padding-bottom:10px!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{opacity:1!important;filter:none!important;transform:translate3d(var(--shift),0,0) scale(1)!important;transition:transform .28s cubic-bezier(.16,1,.3,1),opacity .22s ease!important}html body #zpShowcaseWhite .zpShowcaseWhite__media img{filter:saturate(1) brightness(.98)!important;transition:transform .42s cubic-bezier(.16,1,.3,1),opacity .24s ease!important}html body #zpShowcaseWhite .zpShowcaseWhite__floatingProgress{backdrop-filter:none!important;-webkit-backdrop-filter:none!important;box-shadow:0 16px 44px rgba(7,20,38,.08)!important}html body #zpShowcaseWhite.is-switching .zpShowcaseWhite__stage{opacity:1!important;transform:none!important}}@media (max-width:760px){html body .zpShowcaseDarkCta{display:block!important;visibility:visible!important;height:auto!important;min-height:0!important;max-height:none!important;margin:0!important;padding:58px 0 74px!important;overflow:hidden!important;pointer-events:auto!important}html body .zpShowcaseDarkCta__inner{width:calc(100% - 20px)!important;grid-template-columns:1fr!important;gap:28px!important}html body .zpShowcaseDarkCta h2{font-size:clamp(34px,10vw,48px)!important;line-height:.98!important;letter-spacing:-.056em!important}html body .zpShowcaseDarkCta p{font-size:13px!important;line-height:1.62!important}html body .zpShowcaseDarkCta__frame{min-height:260px!important;border-radius:24px!important}html body .zpShowcaseDarkCta__actions{display:grid!important;grid-template-columns:1fr!important}html body .zpShowcaseDarkCta__btn{width:100%!important;min-height:54px!important}html body .zpShowcaseDarkCta__mark{left:50%!important;right:auto!important;bottom:-.22em!important;transform:translateX(-50%)!important;font-size:clamp(90px,25vw,170px)!important}}@media (min-width:761px){html body #zpShowcaseWhite{--zpPortfolioSwitchH:58px}html body #zpShowcaseWhite .zpShowcaseWhite__switch,html body #zpShowcaseWhite .zpShowcaseWhite__switch--desktop,html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating{display:grid!important;grid-template-columns:1fr 1fr!important;align-items:center!important;width:100%!important;max-width:560px!important;min-height:var(--zpPortfolioSwitchH)!important;height:var(--zpPortfolioSwitchH)!important;padding:var(--zpPortfolioSwitchPad)!important;gap:0!important;border-radius:999px!important;background:linear-gradient(180deg,#ffffff 0%,#f3f5f8 100%)!important;border:1px solid rgba(7,20,38,.12)!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.95),0 12px 34px rgba(7,20,38,.08)!important;overflow:hidden!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__side .zpShowcaseWhite__switch{margin:0 0 18px!important;max-width:560px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating{max-width:420px!important;min-height:48px!important;height:48px!important;padding:4px!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.95),0 10px 24px rgba(7,20,38,.10)!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{position:relative!important;z-index:3!important;width:100%!important;min-width:0!important;height:calc(var(--zpPortfolioSwitchH) - (var(--zpPortfolioSwitchPad) * 2))!important;min-height:calc(var(--zpPortfolioSwitchH) - (var(--zpPortfolioSwitchPad) * 2))!important;padding:0 16px!important;display:flex!important;gap:8px!important;border:0!important;border-radius:999px!important;background:transparent!important;box-shadow:none!important;color:rgba(7,20,38,.68)!important;font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,sans-serif!important;font-size:14px!important;line-height:1!important;letter-spacing:-.018em!important;font-weight:760!important;white-space:nowrap!important;overflow:hidden!important;text-transform:none!important;transition:color .22s ease,transform .22s cubic-bezier(.16,1,.3,1)!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchBtn{height:40px!important;min-height:40px!important;padding:0 11px!important;gap:6px!important;font-size:12.5px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn span{display:block!important;min-width:0!important;max-width:100%!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important;color:inherit!important;font-size:inherit!important;line-height:1!important;font-weight:inherit!important;letter-spacing:inherit!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon svg{display:block!important;width:16px!important;height:16px!important;min-width:16px!important;flex:0 0 16px!important;color:currentColor!important;stroke:currentColor!important;opacity:.9!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchIcon svg{width:14px!important;height:14px!important;min-width:14px!important;flex-basis:14px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn.is-active{color:#fff!important;transform:translate3d(0,-.5px,0)!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchThumb{display:block!important;position:absolute!important;z-index:1!important;top:var(--zpPortfolioSwitchPad)!important;left:var(--zpPortfolioSwitchPad)!important;width:calc(50% - var(--zpPortfolioSwitchPad))!important;height:calc(100% - (var(--zpPortfolioSwitchPad) * 2))!important;border-radius:999px!important;background:linear-gradient(105deg,#03070d 0%,#071426 40%,#102a4f 72%,#1c477a 100%)!important;box-shadow:0 10px 24px rgba(7,20,38,.22)!important;transform:translate3d(0,0,0)!important;transition:transform .34s cubic-bezier(.16,1,.3,1)!important;pointer-events:none!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__switchThumb{transform:translate3d(100%,0,0)!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{will-change:transform!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{transition:box-shadow .22s ease,border-color .22s ease,opacity .22s ease!important}}@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile{grid-template-columns:1fr 1fr!important;min-height:54px!important;height:54px!important;gap:0!important;background:#f5f6f8!important;border:1px solid rgba(7,20,38,.10)!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{height:44px!important;min-height:44px!important;padding:0 8px!important;gap:6px!important;font-size:12.5px!important;border-radius:999px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon svg{width:14px!important;height:14px!important;min-width:14px!important;flex-basis:14px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchThumb{top:5px!important;left:5px!important;width:calc(50% - 5px)!important;height:calc(100% - 10px)!important;border-radius:999px!important}}@media (min-width:761px){html body #zpShowcaseWhite{--zpPortfolioFloatTop:clamp(184px,15vh,236px);--zpPortfolioFlowLift:-64px;--zpPortfolioSwitchH:62px;--zpPortfolioSwitchPad:5px}html body #zpShowcaseWhite .zpShowcaseWhite__floatingProgress{top:var(--zpPortfolioFloatTop)!important;width:min(452px,calc(100% - 40px))!important;padding:12px!important;border-radius:30px!important;overflow:visible!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;transform:translate3d(-50%,18px,0) scale(.985)!important;transition:opacity .26s ease,transform .32s cubic-bezier(.16,1,.3,1),visibility 0s linear .32s!important}html body #zpShowcaseWhite.is-flowing .zpShowcaseWhite__floatingProgress{opacity:1!important;visibility:visible!important;pointer-events:auto!important;transform:translate3d(-50%,0,0) scale(1)!important;transition:opacity .24s ease,transform .32s cubic-bezier(.16,1,.3,1),visibility 0s!important}html body #zpShowcaseWhite.is-flowing .zpShowcaseWhite__stage{margin-top:var(--zpPortfolioFlowLift)!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch,html body #zpShowcaseWhite .zpShowcaseWhite__switch--desktop,html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating{overflow:visible!important;isolation:isolate!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{font-size:16px!important;line-height:1.18!important;overflow:visible!important;padding-top:1px!important;padding-bottom:2px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn span{display:inline-flex!important;align-items:center!important;justify-content:center!important;overflow:visible!important;text-overflow:clip!important;line-height:1.18!important;padding-bottom:1px!important;transform:translateY(0)!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating{min-height:52px!important;height:52px!important;opacity:0!important;visibility:hidden!important;transform:translate3d(0,10px,0) scale(.985)!important;transition:opacity .24s ease .04s,transform .32s cubic-bezier(.16,1,.3,1) .04s,visibility 0s linear .32s!important}html body #zpShowcaseWhite.is-flowing .zpShowcaseWhite__switch--floating{opacity:1!important;visibility:visible!important;pointer-events:auto!important;transform:translate3d(0,0,0) scale(1)!important;transition:opacity .24s ease .04s,transform .32s cubic-bezier(.16,1,.3,1) .04s,visibility 0s!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__switchBtn{height:44px!important;min-height:44px!important;font-size:14.5px!important;line-height:1.18!important;padding:1px 12px 2px!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint{position:absolute!important;z-index:8!important;right:-10px!important;top:-16px!important;width:34px!important;background:#fff!important;pointer-events:none!important;opacity:.96!important;animation:zpPortfolioTapHint 2.7s cubic-bezier(.16,1,.3,1) infinite!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHand{display:block!important;font-size:17px!important;line-height:1!important;transform-origin:50% 80%!important;animation:zpPortfolioTapFinger 2.7s cubic-bezier(.16,1,.3,1) infinite!important}}@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile{min-height:58px!important;height:58px!important;padding:5px!important;margin-top:20px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{height:48px!important;min-height:48px!important;font-size:14.5px!important;line-height:1.18!important;padding:1px 8px 2px!important;overflow:visible!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn span{overflow:visible!important;text-overflow:clip!important;line-height:1.18!important;padding-bottom:1px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switchIcon svg{width:15px!important;height:15px!important;min-width:15px!important;flex-basis:15px!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint{position:absolute!important;z-index:8!important;right:-6px!important;top:-14px!important;width:31px!important;background:#fff!important;pointer-events:none!important;animation:zpPortfolioTapHint 2.9s cubic-bezier(.16,1,.3,1) infinite!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHand{display:block!important;font-size:16px!important;line-height:1!important;transform-origin:50% 80%!important;animation:zpPortfolioTapFinger 2.9s cubic-bezier(.16,1,.3,1) infinite!important}}@keyframes zpPortfolioTapHint{0%,74%,100%{transform:translate3d(0,0,0) scale(1);opacity:.92}12%{transform:translate3d(-4px,2px,0) scale(.96);opacity:1}24%{transform:translate3d(0,0,0) scale(1);opacity:.96}}@keyframes zpPortfolioTapFinger{0%,74%,100%{transform:rotate(-8deg) translate3d(0,0,0)}12%{transform:rotate(-8deg) translate3d(-3px,2px,0) scale(.94)}24%{transform:rotate(-8deg) translate3d(0,0,0) scale(1)}}@media (min-width:761px){html body #zpShowcaseWhite .zpShowcaseWhite__head{padding-top:40px!important}html body #zpShowcaseWhite:not(.is-flowing) .zpShowcaseWhite__floatingProgress,html body #zpShowcaseWhite:not(.is-flowing) .zpShowcaseWhite__switch--floating{display:none!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important}html body #zpShowcaseWhite.is-flowing .zpShowcaseWhite__head .zpShowcaseWhite__switch--desktop{opacity:0!important;visibility:hidden!important;pointer-events:none!important}html body #zpShowcaseWhite.is-flowing .zpShowcaseWhite__floatingProgress{display:block!important}html body #zpShowcaseWhite.is-flowing .zpShowcaseWhite__switch--floating{display:grid!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint{top:-20px!important;width:32px!important;height:32px!important;transform:translate3d(-50%,0,0)!important;animation:zpPortfolioSwipeHint 3.2s cubic-bezier(.16,1,.3,1) infinite!important;opacity:.92!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHand{font-size:16px!important;transform-origin:50% 90%!important;animation:zpPortfolioSwipeHand 3.2s cubic-bezier(.16,1,.3,1) infinite!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating .zpShowcaseWhite__tapHint{display:none!important}}@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__head{padding-top:28px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--desktop,html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating,html body #zpShowcaseWhite .zpShowcaseWhite__floatingProgress{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint{top:-17px!important;width:30px!important;height:30px!important;transform:translate3d(-50%,0,0)!important;animation:zpPortfolioSwipeHintMobile 3.4s cubic-bezier(.16,1,.3,1) infinite!important;opacity:.92!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHand{font-size:15px!important;transform-origin:50% 90%!important;animation:zpPortfolioSwipeHand 3.4s cubic-bezier(.16,1,.3,1) infinite!important}}@keyframes zpPortfolioSwipeHint{0%,100%{transform:translate3d(calc(-50% - 42px),0,0) scale(1);opacity:.58}18%{transform:translate3d(calc(-50% - 42px),0,0) scale(1);opacity:.9}46%{transform:translate3d(calc(-50% + 42px),0,0) scale(.98);opacity:1}66%{transform:translate3d(calc(-50% + 42px),0,0) scale(1);opacity:.76}82%{transform:translate3d(calc(-50% - 42px),0,0) scale(1);opacity:.66}}@keyframes zpPortfolioSwipeHintMobile{0%,100%{transform:translate3d(calc(-50% - 34px),0,0) scale(1);opacity:.58}18%{transform:translate3d(calc(-50% - 34px),0,0) scale(1);opacity:.9}46%{transform:translate3d(calc(-50% + 34px),0,0) scale(.98);opacity:1}66%{transform:translate3d(calc(-50% + 34px),0,0) scale(1);opacity:.76}82%{transform:translate3d(calc(-50% - 34px),0,0) scale(1);opacity:.66}}@keyframes zpPortfolioSwipeHand{0%,100%{transform:rotate(-12deg) translate3d(0,0,0)}36%{transform:rotate(-4deg) translate3d(3px,-1px,0)}48%{transform:rotate(2deg) translate3d(5px,-1px,0) scale(.96)}68%{transform:rotate(-8deg) translate3d(0,0,0)}}@media (min-width:761px){html body #zpShowcaseWhite{--zpPortfolioFloatTop:clamp(196px,16vh,252px)}html body #zpShowcaseWhite .zpShowcaseWhite__switch--desktop,html body #zpShowcaseWhite .zpShowcaseWhite__switch--floating{overflow:visible!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{display:grid!important;grid-template-columns:auto minmax(0,auto)!important;align-items:center!important;justify-content:center!important;column-gap:9px!important;line-height:1.22!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint{left:50%!important;right:auto!important;top:-22px!important;width:42px!important;height:34px!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:2px!important;border-radius:999px!important;background:rgba(255,255,255,.98)!important;border:1px solid rgba(7,20,38,.10)!important;box-shadow:0 12px 30px rgba(7,20,38,.14)!important;transform:translate3d(calc(-50% - 44px),0,0)!important;animation:zpPortfolioLucideSwipeHint 3.6s cubic-bezier(.16,1,.3,1) infinite!important;opacity:.94!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHand,html body #zpShowcaseWhite .zpShowcaseWhite__tapHand svg{width:17px!important;height:17px!important;flex:0 0 17px!important;stroke:#071426!important;stroke-width:2.05!important;animation:zpPortfolioLucideHandPress 3.6s cubic-bezier(.16,1,.3,1) infinite!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapArrow,html body #zpShowcaseWhite .zpShowcaseWhite__tapArrow svg{width:13px!important;height:13px!important;flex:0 0 13px!important;stroke:#102a4f!important;stroke-width:2.2!important;opacity:.75!important;animation:zpPortfolioLucideArrowPulse 3.6s cubic-bezier(.16,1,.3,1) infinite!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__tapHint{transform:translate3d(calc(-50% + 44px),0,0)!important;animation-name:zpPortfolioLucideSwipeHintBack!important}}@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile{width:100%!important;display:grid!important;grid-template-columns:minmax(0,1fr) minmax(0,1fr)!important;align-items:center!important;min-height:60px!important;height:60px!important;padding:6px!important;margin:22px 0 0!important;overflow:visible!important;border-radius:999px!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile .zpShowcaseWhite__switchBtn{width:100%!important;height:48px!important;min-height:48px!important;display:grid!important;grid-template-columns:auto minmax(0,auto)!important;align-items:center!important;justify-content:center!important;column-gap:8px!important;padding:0 9px!important;font-size:14.5px!important;line-height:1.22!important;letter-spacing:-.032em!important;overflow:visible!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile .zpShowcaseWhite__switchBtn span{display:block!important;min-width:0!important;overflow:visible!important;text-overflow:clip!important;line-height:1.22!important;padding:0 0 2px!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile .zpShowcaseWhite__switchIcon,html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile .zpShowcaseWhite__switchIcon svg{display:block!important;width:16px!important;height:16px!important;min-width:16px!important;flex:0 0 16px!important;margin:0!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile .zpShowcaseWhite__switchThumb{top:6px!important;left:6px!important;width:calc(50% - 6px)!important;height:calc(100% - 12px)!important;border-radius:999px!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint{left:50%!important;right:auto!important;top:-18px!important;width:40px!important;height:31px!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:1px!important;border-radius:999px!important;background:rgba(255,255,255,.98)!important;border:1px solid rgba(7,20,38,.10)!important;box-shadow:0 10px 24px rgba(7,20,38,.13)!important;transform:translate3d(calc(-50% - 34px),0,0)!important;animation:zpPortfolioLucideSwipeHintMobile 3.7s cubic-bezier(.16,1,.3,1) infinite!important;opacity:.94!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHand,html body #zpShowcaseWhite .zpShowcaseWhite__tapHand svg{width:16px!important;height:16px!important;flex:0 0 16px!important;stroke:#071426!important;stroke-width:2.05!important;animation:zpPortfolioLucideHandPress 3.7s cubic-bezier(.16,1,.3,1) infinite!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapArrow,html body #zpShowcaseWhite .zpShowcaseWhite__tapArrow svg{width:12px!important;height:12px!important;flex:0 0 12px!important;stroke:#102a4f!important;stroke-width:2.25!important;opacity:.75!important;animation:zpPortfolioLucideArrowPulse 3.7s cubic-bezier(.16,1,.3,1) infinite!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__tapHint{transform:translate3d(calc(-50% + 34px),0,0)!important;animation-name:zpPortfolioLucideSwipeHintMobileBack!important}}@keyframes zpPortfolioLucideSwipeHint{0%,100%{transform:translate3d(calc(-50% - 44px),0,0);opacity:.58}14%{transform:translate3d(calc(-50% - 44px),0,0) scale(.98);opacity:1}28%{transform:translate3d(calc(-50% - 44px),0,0) scale(.94);opacity:1}58%{transform:translate3d(calc(-50% + 44px),0,0) scale(1);opacity:1}76%{transform:translate3d(calc(-50% + 44px),0,0);opacity:.72}}@keyframes zpPortfolioLucideSwipeHintBack{0%,100%{transform:translate3d(calc(-50% + 44px),0,0);opacity:.58}14%{transform:translate3d(calc(-50% + 44px),0,0) scale(.98);opacity:1}28%{transform:translate3d(calc(-50% + 44px),0,0) scale(.94);opacity:1}58%{transform:translate3d(calc(-50% - 44px),0,0) scale(1);opacity:1}76%{transform:translate3d(calc(-50% - 44px),0,0);opacity:.72}}@keyframes zpPortfolioLucideSwipeHintMobile{0%,100%{transform:translate3d(calc(-50% - 34px),0,0);opacity:.58}14%{transform:translate3d(calc(-50% - 34px),0,0) scale(.98);opacity:1}28%{transform:translate3d(calc(-50% - 34px),0,0) scale(.94);opacity:1}58%{transform:translate3d(calc(-50% + 34px),0,0) scale(1);opacity:1}76%{transform:translate3d(calc(-50% + 34px),0,0);opacity:.72}}@keyframes zpPortfolioLucideSwipeHintMobileBack{0%,100%{transform:translate3d(calc(-50% + 34px),0,0);opacity:.58}14%{transform:translate3d(calc(-50% + 34px),0,0) scale(.98);opacity:1}28%{transform:translate3d(calc(-50% + 34px),0,0) scale(.94);opacity:1}58%{transform:translate3d(calc(-50% - 34px),0,0) scale(1);opacity:1}76%{transform:translate3d(calc(-50% - 34px),0,0);opacity:.72}}@keyframes zpPortfolioLucideHandPress{0%,100%{transform:rotate(-10deg) translate3d(0,0,0) scale(1)}22%{transform:rotate(-10deg) translate3d(0,1px,0) scale(.88)}42%{transform:rotate(-4deg) translate3d(2px,0,0) scale(.94)}68%{transform:rotate(3deg) translate3d(3px,0,0) scale(1)}}@keyframes zpPortfolioLucideArrowPulse{0%,100%{opacity:.35;transform:translate3d(-2px,0,0)}30%{opacity:.95;transform:translate3d(0,0,0)}58%{opacity:.95;transform:translate3d(3px,0,0)}}@media(max-width: 760px){html body #zpShowcaseWhite .zpShowcaseWhite__spacer{height:auto!important;min-height:0!important}html body #zpShowcaseWhite .zpShowcaseWhite__sticky{position:relative!important;top:auto!important;height:auto!important;min-height:0!important;transform:none!important;contain:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage{width:100vw!important;max-width:100vw!important;margin-left:calc(50% - 50vw)!important;overflow-x:auto!important;overflow-y:hidden!important;height:auto!important;min-height:min(132vw,680px)!important;max-height:none!important;-webkit-overflow-scrolling:touch!important;scroll-snap-type:none!important;scroll-behavior:auto!important;touch-action:pan-x pan-y!important;overscroll-behavior-x:contain!important;overscroll-behavior-y:auto!important;contain:layout paint!important;transform:none!important;will-change:auto!important;scrollbar-width:none!important;padding:2px 0 16px!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage::-webkit-scrollbar{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{width:max-content!important;max-width:none!important;min-height:min(132vw,680px)!important;display:flex!important;flex-wrap:nowrap!important;grid-template-columns:none!important;gap:14px!important;padding:0 6vw!important;align-items:flex-start!important;transform:none!important;will-change:auto!important;transition:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{width:86vw!important;flex:0 0 86vw!important;aspect-ratio:9 / 13.1!important;max-height:none!important;min-height:0!important;transform:none!important;opacity:1!important;scroll-snap-align:none!important;scroll-snap-stop:normal!important;will-change:auto!important;transition:none!important;contain:layout paint style!important;backface-visibility:hidden!important;-webkit-backface-visibility:hidden!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{width:86vw!important;flex-basis:86vw!important;aspect-ratio:1 / 1!important}html body #zpShowcaseWhite .zpShowcaseWhite__floatingProgress{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__bottom{display:flex!important}html body .zpShowcaseDarkCta__mark{color:rgba(255,255,255,.045)!important;opacity:.18!important;filter:none!important;text-shadow:none!important;-webkit-text-fill-color:rgba(255,255,255,.045)!important;background:none!important;mask-image:linear-gradient(180deg,transparent 0%,#000 46%,transparent 100%)!important;-webkit-mask-image:linear-gradient(180deg,transparent 0%,#000 46%,transparent 100%)!important}}</style>

<style id="zp-suite-v2199-mobile-portfolio-light-polish">@media(max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__stage{contain:layout paint!important;content-visibility:visible!important;overscroll-behavior-x:contain!important}html body #zpShowcaseWhite .zpShowcaseWhite__card,html body #zpShowcaseWhite .zpShowcaseWhite__media,html body #zpShowcaseWhite .zpShowcaseWhite__media img{will-change:auto!important;transition:none!important;animation:none!important}html body .zpShowcaseDarkCta__mark{color:rgba(255,255,255,.026)!important;-webkit-text-fill-color:rgba(255,255,255,.026)!important;opacity:.10!important;filter:none!important;text-shadow:none!important;mix-blend-mode:normal!important}}</style>


<style id="zp-suite-v2200-mobile-portfolio-edge-fix">@media(max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__stage{width:100vw!important;max-width:100vw!important;overflow-x:auto!important;padding-left:0!important;padding-right:0!important;scroll-padding-left:max(18px,6vw)!important;scroll-padding-right:max(18px,6vw)!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{padding-left:max(18px,6vw)!important;padding-right:max(18px,6vw)!important;gap:14px!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{width:84vw!important;flex-basis:84vw!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{width:84vw!important;flex-basis:84vw!important}}</style>

<style id="zp-suite-v2201-mobile-portfolio-cta-edge-fix">@media(max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__stage{padding-left:0!important;padding-right:0!important;scroll-padding-left:18px!important;scroll-padding-right:18px!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{padding-left:18px!important;padding-right:18px!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{width:calc(100vw - 36px)!important;flex-basis:calc(100vw - 36px)!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{width:calc(100vw - 36px)!important;flex-basis:calc(100vw - 36px)!important}html body #zpShowcaseWhite .zpShowcaseWhite__card--cta{margin-right:0!important}html body #zpShowcaseWhite .zpShowcaseWhite__ctaFrame{inset:22px!important;padding:24px 18px!important}html body #zpShowcaseWhite .zpShowcaseWhite__ctaContent{max-width:100%!important;margin-inline:auto!important;text-align:center!important}}</style>


<style id="zp-suite-v2213-mobile-portfolio-crash-final">@media(max-width:760px){html body #zpShowcaseWhite{contain:layout paint!important;overflow:hidden!important;content-visibility:auto!important;contain-intrinsic-size:1200px!important}html body #zpShowcaseWhite .zpShowcaseWhite__inner,html body #zpShowcaseWhite .zpShowcaseWhite__head,html body #zpShowcaseWhite .zpShowcaseWhite__stage,html body #zpShowcaseWhite .zpShowcaseWhite__rail{transform:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;box-shadow:none!important;animation:none!important;transition:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage{contain:strict!important;height:min(132vw,680px)!important;min-height:0!important;isolation:isolate!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{height:min(132vw,680px)!important;min-height:0!important;align-items:stretch!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{height:min(132vw,680px)!important;max-height:min(132vw,680px)!important;contain:strict!important;clip-path:none!important;border-radius:22px!important;overflow:hidden!important;transform:none!important;filter:none!important;box-shadow:none!important;transition:none!important;animation:none!important;will-change:auto!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{height:calc(100vw - 36px)!important;max-height:calc(100vw - 36px)!important}html body #zpShowcaseWhite .zpShowcaseWhite__media{contain:strict!important;transform:none!important;filter:none!important;background:#05070b!important}html body #zpShowcaseWhite .zpShowcaseWhite__media img{width:100%!important;height:100%!important;object-fit:cover!important;object-position:center center!important;transform:none!important;filter:none!important;transition:none!important;animation:none!important;will-change:auto!important;backface-visibility:hidden!important;-webkit-backface-visibility:hidden!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint,html body #zpShowcaseWhite .zpShowcaseWhite__floatingProgress,html body #zpShowcaseWhite .zpShowcaseWhite__meta,html body #zpShowcaseWhite .zpShowcaseWhite__veil::before{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__top,html body #zpShowcaseWhite .zpShowcaseWhite__content,html body #zpShowcaseWhite .zpShowcaseWhite__actions,html body #zpShowcaseWhite .zpShowcaseWhite__btn{transform:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;transition:none!important;animation:none!important;will-change:auto!important}}</style>
<script id="zp-home-portfolio-core-js">
(function(){
  const root = document.querySelector('[data-zp-showcase-white]');
  if (!root || root.dataset.zpReady === '1') return;
  const zpInitialMobile = (window.innerWidth || document.documentElement.clientWidth || 0) <= 760;
  if (zpInitialMobile) {
    root.dataset.zpReady = 'mobile-static';
    root.classList.add('is-visible','is-mobile-static');
    return;
  }
  root.dataset.zpReady = '1';

  const zpPortfolioCreateIcons = (scope) => {
    const target = scope || root || document;

    /* On the homepage Lucide is intentionally guarded against expensive global
       DOM scans. Dynamic modal markup therefore has to use the scoped icon
       hydrator owned by the global header controller. */
    if (typeof window.zpSuiteHydrateIcons === 'function') {
      try {
        const hydrated = window.zpSuiteHydrateIcons(target);
        if (hydrated !== false) return true;
      } catch(e) {}
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      try {
        window.__zpAllowLucideScan = true;
        window.lucide.createIcons({
          attrs: {
            'stroke-width': 2,
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'aria-hidden': 'true'
          }
        });
        return true;
      } catch(e) {
        return false;
      } finally {
        window.__zpAllowLucideScan = false;
      }
    }

    return false;
  };
  zpPortfolioCreateIcons(root);
  window.addEventListener('load', () => zpPortfolioCreateIcons(root), { once: true, passive: true });

  const spacer = root.querySelector('[data-zp-showcase-spacer]');
  const stage = root.querySelector('[data-zp-showcase-stage]');
  const rail = root.querySelector('[data-zp-showcase-rail]');
  const titleEl = root.querySelector('[data-zp-showcase-title]');
  const leadEl = root.querySelector('[data-zp-showcase-lead]');
  const kickerEl = root.querySelector('[data-zp-showcase-kicker]');
  const tipEl = root.querySelector('[data-zp-showcase-tip]');
  const bottomTipEl = root.querySelector('[data-zp-showcase-bottom-tip]');
  const modeBtns = root.querySelectorAll('[data-zp-mode-btn]');
  const currentEls = root.querySelectorAll('[data-zp-showcase-current]');
  const totalEls = root.querySelectorAll('[data-zp-showcase-total]');
  const barEls = root.querySelectorAll('[data-zp-showcase-bar]');
  const prevBtn = root.querySelector('[data-zp-showcase-prev]');
  const nextBtn = root.querySelector('[data-zp-showcase-next]');

  const pop = document.getElementById('zpShowcasePop');
  const popBody = pop ? pop.querySelector('[data-zp-pop-body]') : null;
  const popCloseEls = pop ? pop.querySelectorAll('[data-zp-pop-close]') : [];

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const clamp = (n, min, max) => Math.min(Math.max(n, min), max);

  let maxTranslate = 0;
  let scrollDistance = 0;
  let startDelay = 0;
  let ticking = false;
  let savedScroll = 0;
  let measured = false;
  let lastWidth = window.innerWidth || document.documentElement.clientWidth || 0;
  let lastStableVH = 0;
  let resizeTimer = null;
  let activeMode = 'web';
  let currentX = 0;
  let targetX = 0;
  let xAnimRaf = 0;
  let vhLock = 0;
  let isInsideTrack = false;
  let lastScrollY = window.scrollY || window.pageYOffset || 0;
  let railPointerDown = false;
  let railPointerStartX = 0;
  let railPointerStartY = 0;
  let railDragged = false;
  let suppressClickUntil = 0;
  let mobileTouchStartX = 0;
  let mobileTouchStartY = 0;
  let mobileTouchMoved = false;
  let mobileSwitchLock = false;
  let mobileActiveIndex = 0;
  let mobileSnapTimer = null;
  let mobileProgrammaticScroll = false;
  let cardMetrics = [];
  let lastActiveIndex = -1;
  let lastProgressPct = -1;
  let lastRenderedX = null;
  let portfolioInView = true;
  let lastStateX = null;

  if (!rail || !spacer || !stage) return;

  const arrowIcon = () => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>';
  const isMobileView = () => (window.innerWidth || document.documentElement.clientWidth || 0) <= 1500;

  const copy = {
    web: {
      kicker: 'Portfolio / strony i sklepy',
      title: '<strong>Strony, sklepy i systemy,</strong> <span>które realnie pracują</span> na wizerunek i sprzedaż.',
      lead: 'Zobacz wybrane projekty, które łączą projekt graficzny, WordPress, WooCommerce, SEO, analitykę, rezerwacje, automatyzacje i kampanie w jeden dopracowany system.',
      tip: 'Przewiń w dół — realizacje stron przejdą poziomo. Możesz też przełączyć widok na logo i branding.',
      bottom: 'Scrolluj dalej lub przełącz na logo'
    },
    logo: {
      kicker: 'Portfolio / logo i branding',
      title: '<strong>Logo, systemy wizualne i brandingi,</strong> <span>które nadają marce</span> profesjonalny charakter.',
      lead: 'Zobacz wybrane projekty logo, sygnety, monogramy, systemy identyfikacji, mockupy i materiały firmowe przygotowane dla różnych branż.',
      tip: 'Przewiń w dół — realizacje logo przejdą poziomo. Karty są kwadratowe, a szczegóły pokażą zakres brandingu.',
      bottom: 'Scrolluj dalej lub wróć do stron'
    }
  };

  const webItems = <?php echo zp_suite_json(zp_suite_portfolio_items('web')); ?>;

  const logoItems = <?php echo zp_suite_json(zp_suite_portfolio_items('logo')); ?>;

  function esc(str){
    return String(str || '').replace(/[&<>"']/g, function(m){
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m];
    });
  }

  function currentItems(){ return activeMode === 'logo' ? logoItems : webItems; }
  function setText(nodeList, value){ nodeList.forEach(el => el.textContent = value); }
  function setBar(nodeList, value){ nodeList.forEach(el => el.style.width = value); }

  function cacheCardMetrics(){
    const cards = Array.from(rail.querySelectorAll('[data-zp-card]'));
    cardMetrics = cards.map((card) => ({
      el: card,
      center: card.offsetLeft + card.offsetWidth * .5
    }));
    lastActiveIndex = -1;
    lastProgressPct = -1;
    lastRenderedX = null;
    lastStateX = null;
    cards.forEach(card => card.style.setProperty('--shift', '0px'));
  }

  function updateCardStateFromX(x, force){
    const items = currentItems();
    if (!cardMetrics.length || !items.length) cacheCardMetrics();
    if (!cardMetrics.length || !items.length) return;

    const roundedForState = Math.round(x / 24) * 24;
    if (!force && lastStateX !== null && Math.abs(roundedForState - lastStateX) < 24) return;
    lastStateX = roundedForState;

    const center = Math.abs(x) + stage.clientWidth * .50;
    let bestIndex = 0;
    let bestDistance = Infinity;

    for (let i = 0; i < cardMetrics.length; i++) {
      const dist = Math.abs(cardMetrics[i].center - center);
      if (dist < bestDistance) { bestDistance = dist; bestIndex = i; }
    }

    if (force || bestIndex !== lastActiveIndex) {
      for (let i = 0; i < cardMetrics.length; i++) {
        const card = cardMetrics[i].el;
        card.classList.toggle('is-active', i === bestIndex);
        card.classList.toggle('is-past', i < bestIndex);
      }
      setText(currentEls, String(bestIndex + 1).padStart(2, '0'));
      lastActiveIndex = bestIndex;
    }

    const progressPct = Math.round(((bestIndex + 1) / items.length) * 1000) / 10;
    if (force || Math.abs(progressPct - lastProgressPct) >= .5) {
      setBar(barEls, `${progressPct}%`);
      lastProgressPct = progressPct;
    }
  }

  function writeRailTransform(x, force){
    const roundedX = Math.round(x * 10) / 10;
    if (!force && lastRenderedX !== null && Math.abs(roundedX - lastRenderedX) < .15) return;
    lastRenderedX = roundedX;
    rail.style.transform = `translate3d(${roundedX}px,0,0)`;
  }

  function setRailX(nextX, immediate){
    targetX = clamp(nextX, -maxTranslate, 0);
    const mobile = isMobileView();

    if (xAnimRaf) { cancelAnimationFrame(xAnimRaf); xAnimRaf = 0; }

    currentX = targetX;

    if (mobile) {
      rail.style.transform = 'none';
      lastRenderedX = null;
    } else {
      writeRailTransform(currentX, true);
    }

    updateCardStateFromX(currentX, true);
  }

  function getStableViewportHeight(){
    const raw = window.innerHeight || document.documentElement.clientHeight || 720;
    const mobile = isMobileView();
    if (!mobile) { lastStableVH = raw; return raw; }
    if (vhLock) return vhLock;
    if (!lastStableVH) lastStableVH = raw;
    if (Math.abs(raw - lastStableVH) > 160) lastStableVH = raw;
    return lastStableVH;
  }

  function setViewportVar(){
    const vh = getStableViewportHeight();
    root.style.setProperty('--zpShowcaseVH', (vh * 0.01) + 'px');
  }

  function updateCopy(){
    const c = copy[activeMode];
    if (kickerEl) kickerEl.textContent = c.kicker;
    if (titleEl) titleEl.innerHTML = c.title;
    if (leadEl) leadEl.textContent = c.lead;
    if (tipEl) tipEl.textContent = c.tip;
    if (bottomTipEl) bottomTipEl.textContent = c.bottom;

    modeBtns.forEach(btn => {
      const isActive = btn.getAttribute('data-zp-mode-btn') === activeMode;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    root.setAttribute('data-mode', activeMode);
  }



  function fullUploadUrl(src){
    src = String(src || '');
    if (!src || src.indexOf('/wp-content/uploads/') === -1) return src;
    return src.replace(/-\d+x\d+(\.(?:webp|avif|png|jpe?g|gif))(\?.*)?$/i, '$1$2');
  }

  function portfolioImgAttrs(item, index){
    const src = esc(fullUploadUrl(item && item.img ? item.img : ''));
    const eager = index === 0;
    const base = 'class="zpShowcaseWhite__img" decoding="async" draggable="false" sizes="(max-width: 720px) 92vw, (max-width: 1180px) 58vw, 620px"';

    if (isMobileView()) {
      const blank = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 1280"%3E%3Crect width="900" height="1280" fill="%2305070b"/%3E%3C/svg%3E';
      if (index <= 1) {
        return `src="${src}" loading="${eager ? 'eager' : 'lazy'}" fetchpriority="${eager ? 'high' : 'low'}" ${base}`;
      }
      return `src="${blank}" data-zp-lazy-src="${src}" loading="lazy" fetchpriority="low" ${base}`;
    }

    return `src="${src}" loading="${eager ? 'eager' : 'lazy'}" fetchpriority="${eager ? 'high' : 'low'}" ${base}`;
  }

  let mobileImgObserver = null;
  function setupMobileImageLazy(){
    if (!isMobileView()) return;
    const imgs = Array.from(rail.querySelectorAll('img[data-zp-lazy-src]'));
    if (!imgs.length) return;

    const loadImg = (img) => {
      const src = img.getAttribute('data-zp-lazy-src');
      if (!src) return;
      img.src = src;
      img.removeAttribute('data-zp-lazy-src');
      img.loading = 'lazy';
      img.decoding = 'async';
    };

    if ('IntersectionObserver' in window) {
      if (mobileImgObserver && typeof mobileImgObserver.disconnect === 'function') {
        try { mobileImgObserver.disconnect(); } catch(e) {}
      }
      mobileImgObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          loadImg(entry.target);
          try { mobileImgObserver.unobserve(entry.target); } catch(e) {}
        });
      }, { root: stage, rootMargin: '220px 420px', threshold: 0.01 });
      imgs.forEach(img => mobileImgObserver.observe(img));
    } else {
      imgs.slice(0, 3).forEach(loadImg);
    }
  }

  function render(){
    const items = currentItems();

    cardMetrics = [];
    lastActiveIndex = -1;
    lastProgressPct = -1;
    lastRenderedX = null;
    lastStateX = null;

    rail.innerHTML = items.map((item, index) => {
      if (item.isCta) {
        const title = activeMode === 'logo' ? 'Tu może być Twoje logo' : 'Tu może być Twoja strona';
        const text = activeMode === 'logo'
          ? 'Opisz nam markę — przygotujemy kierunek, logo, kolory, typografię, mockupy i materiały firmowe.'
          : 'Opisz nam projekt — dobierzemy zakres, styl, funkcje, SEO i plan wdrożenia.';
        const href = '/studio-wyceny/';

        return `
          <article class="zpShowcaseWhite__card zpShowcaseWhite__card--cta" data-zp-card data-index="${index}" aria-label="${esc(item.brand)}">
            <div class="zpShowcaseWhite__ctaFrame">
              <div class="zpShowcaseWhite__ctaContent">
                <span class="zpShowcaseWhite__ctaEyebrow">Twój projekt może być następny</span>
                <h3 class="zpShowcaseWhite__ctaTitle">${esc(title)}</h3>
                <p class="zpShowcaseWhite__ctaText">${esc(text)}</p>
                <div class="zpShowcaseWhite__ctaActions"><a class="zpShowcaseWhite__ctaBtn" href="${esc(href)}">Otrzymaj darmową wycenę ${arrowIcon()}</a></div>
              </div>
            </div>
          </article>`;
      }

      const meta = (item.meta || []).map(m => `<span>${esc(m)}</span>`).join('');
      const live = item.live ? `<a class="zpShowcaseWhite__btn zpShowcaseWhite__btn--live" href="${esc(item.live)}" target="_blank" rel="noopener">Live ${arrowIcon()}</a>` : '';

      return `
        <article class="zpShowcaseWhite__card" data-zp-card data-index="${index}" tabindex="0" aria-label="Otwórz szczegóły projektu ${esc(item.brand)}">
          <div class="zpShowcaseWhite__media"><img ${portfolioImgAttrs(item, index)} alt="${esc(item.brand)} — ${esc(item.sub)}"></div>
          <div class="zpShowcaseWhite__veil"></div>
          <div class="zpShowcaseWhite__top"><span class="zpShowcaseWhite__pill">${esc(item.type)}</span><span class="zpShowcaseWhite__index">${String(index + 1).padStart(2, '0')}</span></div>
          <div class="zpShowcaseWhite__content">
            <h3 class="zpShowcaseWhite__name">${esc(item.brand)}<span class="zpShowcaseWhite__sub">${esc(item.sub)}</span></h3>
            <div class="zpShowcaseWhite__meta">${meta}</div>
            <div class="zpShowcaseWhite__actions">
              <button type="button" class="zpShowcaseWhite__btn zpShowcaseWhite__btn--primary" data-zp-detail="${index}">Szczegóły ${arrowIcon()}</button>
              ${live}
            </div>
          </div>
        </article>`;
    }).join('');

    setText(totalEls, String(items.length).padStart(2, '0'));
    currentX = 0;
    targetX = 0;
    vhLock = 0;
    isInsideTrack = false;
    setRailX(0, true);

    if (isMobileView()) {
      stage.scrollLeft = 0;
      root.classList.add('is-visible');
      root.classList.remove('is-flowing');
      updateMobileRailState();
      setupMobileImageLazy();
    }

    rail.querySelectorAll('[data-zp-detail]').forEach(btn => {
      btn.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        if (Date.now() < suppressClickUntil || railDragged) return;
        const index = Number(btn.getAttribute('data-zp-detail') || 0);
        const item = currentItems()[index];
        if (item && !item.isCta) openPopup(item);
      });
    });

    rail.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function(e){
        if (Date.now() < suppressClickUntil || railDragged) { e.preventDefault(); e.stopPropagation(); return; }
        e.stopPropagation();
      });
    });

    rail.querySelectorAll('[data-zp-card]').forEach(card => {
      card.addEventListener('click', function(e){
        if (Date.now() < suppressClickUntil || railDragged) { e.preventDefault(); e.stopPropagation(); return; }
        if (e.target.closest('a, button')) return;
        if (isMobileView()) return;
        const index = Number(card.getAttribute('data-index') || 0);
        const item = currentItems()[index];
        if (!item) return;
        if (item.isCta) { window.location.href = '/studio-wyceny/'; return; }
        openPopup(item);
      });

      card.addEventListener('keydown', function(e){
        if (e.key !== 'Enter' && e.key !== ' ') return;
        if (e.target.closest('a, button')) return;
        e.preventDefault();
        const index = Number(card.getAttribute('data-index') || 0);
        const item = currentItems()[index];
        if (!item) return;
        if (item.isCta) { window.location.href = '/studio-wyceny/'; return; }
        openPopup(item);
      });
    });
  }

  function renderPopup(item){
    const isLogo = activeMode === 'logo';
    const live = item.live ? `<a href="${esc(item.live)}" target="_blank" rel="noopener">Zobacz live ${arrowIcon()}</a>` : '';
    const scope = (item.scope || []).map(s => `<li><i data-lucide="check"></i><span>${esc(s)}</span></li>`).join('');
    const tools = (item.tools || []).map(t => `<span class="zpShowcasePop__tool"><i data-lucide="check-circle-2"></i>${esc(t)}</span>`).join('');
    const storyRows = [
      ['target','Cel projektu',item.challenge],
      ['wand-sparkles','Kierunek rozwiązania',item.solution],
      ['trending-up','Efekt dla marki',item.effect]
    ].filter(row => row[2]);
    const story = storyRows.length ? `<div class="zpShowcasePop__story">${storyRows.map((row,index) => `<article class="zpShowcasePop__storyCard${index === 1 ? ' zpShowcasePop__storyCard--navy' : ''}"><span class="zpShowcasePop__storyIcon"><i data-lucide="${row[0]}"></i></span><div><small>${esc(row[1])}</small><p>${esc(row[2])}</p></div></article>`).join('')}</div>` : '';
    const resultRows = Array.isArray(item.results) ? item.results.filter(row => Array.isArray(row) && (row[0] || row[1])) : [];
    const results = resultRows.length ? `<div class="zpShowcasePop__resultsTitle"><i data-lucide="badge-check"></i>Najważniejsze efekty</div><div class="zpShowcasePop__results">${resultRows.map(row => `<article class="zpShowcasePop__result"><strong>${esc(row[0])}</strong><span>${esc(row[1])}</span></article>`).join('')}</div>` : '';
    const primaryHref = isLogo ? '/logo-branding-katowice/' : '/zamowienie-strony-sklepu-internetowego/';
    const primaryText = isLogo ? 'Zamów logo w tym stylu' : 'Zamów podobny projekt';
    const secondaryHref = isLogo ? '/kontakt/' : '/strony-internetowe-katowice/';
    const secondaryText = isLogo ? 'Porozmawiajmy o brandingu' : 'Zobacz ofertę stron';

    return `
      <div class="zpShowcasePop__layout ${isLogo ? 'zpShowcasePop__layout--logo' : 'zpShowcasePop__layout--web'}">
        <div class="zpShowcasePop__visual" style="--pop-img:url('${esc(fullUploadUrl(item.img))}')"><div class="zpShowcasePop__visualInner"><img class="zpShowcasePop__img" src="${esc(fullUploadUrl(item.img))}" alt="${esc(item.brand)} — ${esc(item.sub)}" loading="eager" decoding="async"></div></div>
        <div class="zpShowcasePop__info">
          <div class="zpShowcasePop__top"><span class="zpShowcasePop__pill"><i data-lucide="tag"></i>${esc(item.tag)}</span><span class="zpShowcasePop__pill"><i data-lucide="calendar-days"></i>${esc(item.year || '2026')}</span>${item.live ? '<span class="zpShowcasePop__pill"><i data-lucide="radio"></i>live</span>' : ''}</div>
          <h3 class="zpShowcasePop__title" id="zpShowcasePopTitle">${esc(item.brand)}</h3>
          <p class="zpShowcasePop__subtitle">${esc(item.sub)}</p>
          <p class="zpShowcasePop__text">${esc(item.desc)}</p>
          ${story}
          <div class="zpShowcasePop__grid">
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="building-2"></i>Klient</div><div class="zpShowcasePop__value">${esc(item.client || item.brand)}</div></div>
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="calendar"></i>Rok</div><div class="zpShowcasePop__value">${esc(item.year || '2026')}</div></div>
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="layers-3"></i>Usługi</div><div class="zpShowcasePop__value">${esc(item.services || item.type)}</div></div>
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="sparkles"></i>${isLogo ? 'Styl' : 'Typ'}</div><div class="zpShowcasePop__value">${isLogo ? esc((item.meta || []).join(', ')) : esc(item.type)}</div></div>
          </div>
          ${tools ? `<div class="zpShowcasePop__tools">${tools}</div>` : ''}
          <div><div class="zpShowcasePop__scopeTitle"><i data-lucide="list-checks"></i>Zakres prac</div><ul class="zpShowcasePop__scope">${scope}</ul></div>
          ${results}
          <div class="zpShowcasePop__cta"><a href="${esc(primaryHref)}">${esc(primaryText)} ${arrowIcon()}</a>${live || `<a href="${esc(secondaryHref)}">${esc(secondaryText)} ${arrowIcon()}</a>`}</div>
        </div>
      </div>`;
  }

  function openPopup(item){
    if (!pop || !popBody) return;
    savedScroll = window.scrollY || window.pageYOffset || 0;
    popBody.innerHTML = renderPopup(item);
    pop.hidden = false;
    pop.classList.add('is-open');
    pop.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('zpShowcaseNoScroll');
    document.body.classList.add('zpShowcaseNoScroll');

    /* Hydrate only the freshly injected modal icons. The second pass covers
       slower cached/deferred Lucide loading without rescanning the whole page. */
    requestAnimationFrame(() => {
      zpPortfolioCreateIcons(popBody);
      window.setTimeout(() => zpPortfolioCreateIcons(popBody), 90);
    });
  }

  function closePopup(){
    if (!pop) return;
    pop.classList.remove('is-open');
    pop.setAttribute('aria-hidden', 'true');
    pop.hidden = true;
    document.documentElement.classList.remove('zpShowcaseNoScroll');
    document.body.classList.remove('zpShowcaseNoScroll');
    if (!isMobileView()) requestAnimationFrame(() => window.scrollTo(0, savedScroll));
  }

  popCloseEls.forEach(el => el.addEventListener('click', closePopup));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && pop && pop.classList.contains('is-open')) closePopup(); });

  function bindRailPointerGuard(){
    if (rail.dataset.zpPointerGuard === '1') return;
    rail.dataset.zpPointerGuard = '1';

    function armSuppress(ms){
      suppressClickUntil = Date.now() + (ms || 650);
    }

    rail.addEventListener('touchstart', (e) => {
      const t = e.touches && e.touches[0];
      if (!t) return;
      mobileTouchStartX = t.clientX || 0;
      mobileTouchStartY = t.clientY || 0;
      mobileTouchMoved = false;
      railDragged = false;
    }, { passive: true });

    rail.addEventListener('touchmove', (e) => {
      const t = e.touches && e.touches[0];
      if (!t) return;

      const dx = Math.abs((t.clientX || 0) - mobileTouchStartX);
      const dy = Math.abs((t.clientY || 0) - mobileTouchStartY);

      if (dx > 5 || dy > 7) {
        mobileTouchMoved = true;
        railDragged = true;
        armSuppress(820);
      }
    }, { passive: true });

    rail.addEventListener('touchend', () => {
      if (mobileTouchMoved) armSuppress(900);
      setTimeout(() => {
        mobileTouchMoved = false;
        railDragged = false;
      }, 140);
    }, { passive: true });

    rail.addEventListener('click', (e) => {
      if (!isMobileView()) return;
      if (Date.now() < suppressClickUntil || mobileTouchMoved || railDragged) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
      }
    }, true);

    rail.addEventListener('pointerdown', (e) => {
      railPointerDown = true;
      railDragged = false;
      railPointerStartX = e.clientX || 0;
      railPointerStartY = e.clientY || 0;
    }, { passive: true });

    rail.addEventListener('pointermove', (e) => {
      if (!railPointerDown) return;
      const dx = Math.abs((e.clientX || 0) - railPointerStartX);
      const dy = Math.abs((e.clientY || 0) - railPointerStartY);
      if (dx > 6 || dy > 10) {
        railDragged = true;
        armSuppress(820);
      }
    }, { passive: true });

    const end = () => {
      if (railDragged) armSuppress(900);
      railPointerDown = false;
      /* v2.2.14: mobile crash guard — żadnego snapowania ani liczenia pozycji po geście. */
      if (isMobileView() && railDragged) {
        window.clearTimeout(mobileSnapTimer);
      }
      window.setTimeout(() => { railDragged = false; }, 180);
    };

    rail.addEventListener('pointerup', end, { passive: true });
    rail.addEventListener('pointercancel', end, { passive: true });
    rail.addEventListener('mouseleave', end, { passive: true });
  }

  function getMobileCards(){
    return Array.from(rail.querySelectorAll('[data-zp-card]'));
  }

  function getMobileNearestIndex(){
    const cards = getMobileCards();
    if (!cards.length) return 0;

    const center = stage.scrollLeft + stage.clientWidth * .5;
    let bestIndex = 0;
    let bestDistance = Infinity;

    cards.forEach((card, i) => {
      const cardCenter = card.offsetLeft + card.offsetWidth * .5;
      const dist = Math.abs(cardCenter - center);
      if (dist < bestDistance) {
        bestDistance = dist;
        bestIndex = i;
      }
    });

    return bestIndex;
  }

  function scrollToMobileIndex(index, behavior = 'smooth'){
    if (!isMobileView()) return;

    const cards = getMobileCards();
    if (!cards.length) return;

    const nextIndex = clamp(index, 0, cards.length - 1);
    const card = cards[nextIndex];
    const left = Math.max(0, card.offsetLeft - ((stage.clientWidth - card.offsetWidth) / 2));

    mobileProgrammaticScroll = true;
    mobileActiveIndex = nextIndex;
    stage.scrollTo({ left: left, top: 0, behavior: behavior });
    updateMobileRailState(nextIndex);

    window.clearTimeout(mobileSnapTimer);
    mobileSnapTimer = window.setTimeout(() => {
      mobileProgrammaticScroll = false;
      updateMobileRailState();
    }, behavior === 'auto' ? 80 : 460);
  }

  function snapMobileToNearest(){
    /* v8.4 mobile no-jump: bez programatycznego dosnapowania po geście.
       To właśnie potrafiło odpalać pionowy skok viewportu w Safari/Chrome mobile. */
    if (!isMobileView()) return;
    updateMobileRailState();
  }

  function updateMobileRailState(forcedIndex){
    if (!isMobileView()) return;
    const items = currentItems();
    const cards = getMobileCards();
    if (!cards.length || !items.length) return;

    const bestIndex = typeof forcedIndex === 'number' ? clamp(forcedIndex, 0, cards.length - 1) : getMobileNearestIndex();
    mobileActiveIndex = bestIndex;

    /* v2.2.09: mobile ultra-light — nie przepinamy klas na wszystkich kartach podczas scrolla. */
    if (!isMobileView()) {
      cards.forEach((card, i) => {
        card.classList.toggle('is-active', i === bestIndex);
        card.classList.toggle('is-past', i < bestIndex);
        card.style.setProperty('--shift', '0px');
      });
    }

    setText(currentEls, String(bestIndex + 1).padStart(2, '0'));
    setBar(barEls, `${((bestIndex + 1) / items.length) * 100}%`);
  }

  /* =========================================================
     v8.10 / HOME DESKTOP PORTFOLIO REWRITE
     Logika przeniesiona z sekcji process ze strony /strony-internetowe-katowice/:
     - bez wheel hijacku,
     - bez snapowania desktopowego,
     - sticky + normalny pionowy scroll mapowany 1:1 na poziomy track,
     - scroll w dół = ruch w prawo, scroll w górę = powrót w lewo,
     - brak opóźnionych przeliczeń wyrzucających do środka galerii.
  ========================================================= */

  function measure(options = {}){
    if (prefersReduced) return;

    setViewportVar();

    const viewportW = window.innerWidth || document.documentElement.clientWidth || 1200;
    const viewportH = getStableViewportHeight();
    const mobile = viewportW <= 760;

    if (mobile) {
      maxTranslate = 0;
      scrollDistance = 0;
      startDelay = 0;
      spacer.style.minHeight = 'auto';
      root.classList.add('is-visible');
      root.classList.remove('is-flowing', 'is-ending', 'is-desktop-pin');
      rail.style.transform = 'none';
      lastRenderedX = null;
      updateMobileRailState();
      measured = true;
      ticking = false;
      return;
    }

    root.classList.add('is-desktop-pin');

    const stageW = Math.max(1, stage.clientWidth || viewportW);
    maxTranslate = Math.max(0, rail.scrollWidth - stageW);

    /* v1.9.03: wolniejszy, bardziej premium desktop horizontal flow — dłuższy dystans pinowania. */
    scrollDistance = clamp(maxTranslate * 1.62, 1850, 5200);
    startDelay = clamp(viewportH * 0.08, 56, 110);

    const minH = Math.ceil(viewportH + startDelay + scrollDistance);
    spacer.style.minHeight = minH + 'px';

    cacheCardMetrics();
    measured = true;
    requestUpdate();
  }

  function update(){
    ticking = false;
    if (prefersReduced) return;

    const viewportW = window.innerWidth || document.documentElement.clientWidth || 1200;
    const viewportH = getStableViewportHeight();
    const mobile = viewportW <= 760;

    if (mobile) {
      root.classList.add('is-visible');
      root.classList.remove('is-flowing', 'is-ending', 'is-desktop-pin');
      rail.style.transform = 'none';
      updateMobileRailState();
      return;
    }

    if (!measured) measure({ force: true });

    const rect = spacer.getBoundingClientRect();
    const scrollable = Math.max(1, rect.height - viewportH);
    const p = clamp((-rect.top - startDelay) / scrollDistance, 0, 1);
    const x = -maxTranslate * p;

    root.classList.add('is-visible');
    root.classList.toggle('is-flowing', p > .075 && p < .965);
    root.classList.toggle('is-ending', p >= .94);

    writeRailTransform(x, false);
    currentX = x;
    targetX = x;
    updateCardStateFromX(x, false);

    if (rect.bottom < 0) {
      writeRailTransform(-maxTranslate, true);
      updateCardStateFromX(-maxTranslate, true);
    } else if (rect.top > viewportH) {
      writeRailTransform(0, true);
      updateCardStateFromX(0, true);
      root.classList.remove('is-flowing', 'is-ending');
    }
  }

  function requestUpdate(){
    if (ticking || prefersReduced) return;
    if (!portfolioInView && measured) return;
    ticking = true;
    window.requestAnimationFrame(update);
  }

  function scrollToDesktopProgress(progress, behavior = 'smooth'){
    if (isMobileView() || prefersReduced) return;
    const p = clamp(progress, 0, 1);
    const baseY = spacer.getBoundingClientRect().top + (window.scrollY || window.pageYOffset || 0);
    const y = baseY + startDelay + p * scrollDistance;
    window.scrollTo({ top: y, behavior: behavior });
  }

  function scrollToDesktopIndex(index, behavior = 'smooth'){
    if (isMobileView() || prefersReduced) return;
    const maxIndex = Math.max(0, currentItems().length - 1);
    const idx = clamp(index, 0, maxIndex);
    scrollToDesktopProgress(maxIndex ? idx / maxIndex : 0, behavior);
  }

  function moveToProgress(delta){
    if (prefersReduced) return;

    if (isMobileView()) {
      const base = typeof mobileActiveIndex === 'number' ? mobileActiveIndex : getMobileNearestIndex();
      scrollToMobileIndex(base + delta, 'smooth');
      return;
    }

    const baseIndex = lastActiveIndex > -1 ? lastActiveIndex : 0;
    scrollToDesktopIndex(baseIndex + delta, 'smooth');
  }

  function switchMode(mode){
    if (mode === activeMode || (mode !== 'web' && mode !== 'logo') || mobileSwitchLock) return;

    const mobile = isMobileView();
    mobileSwitchLock = true;
    activeMode = mode;

    if (!mobile) root.classList.add('is-switching');

    const doSwitch = () => {
      updateCopy();
      render();

      currentX = 0;
      targetX = 0;
      lastRenderedX = null;
      vhLock = 0;
      isInsideTrack = false;

      if (mobile) {
        spacer.style.minHeight = 'auto';
        rail.style.transform = 'none';
        root.classList.add('is-visible');
        root.classList.remove('is-flowing', 'is-ending', 'is-switching', 'is-desktop-pin');

        mobileActiveIndex = 0;
        stage.scrollLeft = 0;
        setText(currentEls, '01');
        setBar(barEls, `${(1 / currentItems().length) * 100}%`);
        updateMobileRailState(0);
        mobileSwitchLock = false;
        return;
      }

      measured = false;
      measure({ force: true });
      scrollToDesktopProgress(0, 'auto');
      setText(currentEls, '01');
      setBar(barEls, `${(1 / currentItems().length) * 100}%`);

      window.setTimeout(() => {
        root.classList.remove('is-switching');
        mobileSwitchLock = false;
        measure({ force: true });
      }, 80);
    };

    if (mobile) {
      doSwitch();
    } else {
      window.setTimeout(doSwitch, 120);
    }
  }


  /* ZP v1.8.99 — homepage smooth wheel disabled.
     Poprzedni globalny wheel smoother przechwytywał scroll strony i potrafił ją zablokować.
     Zostawiamy natywny scroll strony + smooth silnik wyłącznie w portfolio. */
  function initHomeSmoothWheel(){
    if (prefersReduced || window.__zpHomeSmoothWheelReady) return;
    if ((window.innerWidth || document.documentElement.clientWidth || 0) <= 760) return;
    window.__zpHomeSmoothWheelReady = true;

    let targetY = window.scrollY || window.pageYOffset || 0;
    let currentY = targetY;
    let raf = 0;
    let userSync = false;

    const maxScroll = () => Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    const stop = () => { if (raf) cancelAnimationFrame(raf); raf = 0; };
    const sync = () => {
      if (userSync) return;
      targetY = window.scrollY || window.pageYOffset || 0;
      currentY = targetY;
    };
    const canSmooth = (e) => {
      if (!e || e.defaultPrevented || e.ctrlKey || e.metaKey || e.shiftKey) return false;
      if (document.documentElement.classList.contains('zpShowcaseNoScroll') || document.body.classList.contains('zpShowcaseNoScroll')) return false;
      const t = e.target;
      if (t && t.closest && t.closest('input,textarea,select,[contenteditable="true"],.zpShowcasePop,.zpShowcaseWhite__stage')) return false;
      const scroller = t && t.closest ? t.closest('[data-native-scroll], .elementor-popup-modal, .dialog-widget-content') : null;
      if (scroller) return false;
      return true;
    };
    const animate = () => {
      const diff = targetY - currentY;
      if (Math.abs(diff) < 0.45) {
        currentY = targetY;
        userSync = true;
        window.scrollTo(0, currentY);
        userSync = false;
        raf = 0;
        return;
      }
      currentY += diff * 0.13;
      userSync = true;
      window.scrollTo(0, currentY);
      userSync = false;
      raf = requestAnimationFrame(animate);
    };

    window.addEventListener('wheel', (e) => {
      if (!canSmooth(e)) return;
      e.preventDefault();
      const multiplier = Math.abs(e.deltaY) < 80 ? 0.92 : 0.78;
      targetY = clamp(targetY + (e.deltaY * multiplier), 0, maxScroll());
      if (!raf) {
        currentY = window.scrollY || window.pageYOffset || 0;
        raf = requestAnimationFrame(animate);
      }
    }, { passive:false });

    window.addEventListener('keydown', (e) => {
      if (['ArrowDown','ArrowUp','PageDown','PageUp','Home','End',' '].includes(e.key)) {
        stop();
        setTimeout(sync, 0);
      }
    }, { passive:true });
    window.addEventListener('resize', () => { targetY = clamp(targetY, 0, maxScroll()); currentY = window.scrollY || window.pageYOffset || 0; }, { passive:true });
    window.addEventListener('scroll', sync, { passive:true });
  }

  modeBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      switchMode(btn.getAttribute('data-zp-mode-btn'));
    });
  });

  if (prevBtn) prevBtn.addEventListener('click', () => moveToProgress(-1));
  if (nextBtn) nextBtn.addEventListener('click', () => moveToProgress(1));

  updateCopy();
  render();
  bindRailPointerGuard();
  setViewportVar();

  if (prefersReduced) {
    root.classList.add('is-visible');
    spacer.style.minHeight = 'auto';
    stage.style.overflowX = 'auto';
    rail.style.transform = 'none';
  } else if (isMobileView()) {
    root.classList.add('is-visible');
    root.classList.remove('is-flowing', 'is-ending', 'is-desktop-pin');
    spacer.style.minHeight = 'auto';
    rail.style.transform = 'none';
    stage.scrollLeft = 0;
    mobileActiveIndex = 0;
    updateMobileRailState(0);

    let mobileLastWidth = window.innerWidth || document.documentElement.clientWidth || 0;
    let mobileRailTicking = false;

    /* v2.2.14 MOBILE STABILITY: bez listenera scroll na railu.
       Na iOS/Chrome mobile ciągłe getBounding/offset + class toggles podczas pionowego wejścia w portfolio
       potrafiły powodować Aw Snap/przeładowanie. Zostaje natywne poziome przewijanie,
       a licznik aktualizują tylko strzałki/przełączenie trybu. */
    if (false) stage.addEventListener('scroll', () => {
      clearTimeout(mobileSnapTimer);
    }, { passive: true });

    window.addEventListener('resize', () => {
      const w = window.innerWidth || document.documentElement.clientWidth || 0;
      if (Math.abs(w - mobileLastWidth) < 24) return;
      mobileLastWidth = w;

      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        spacer.style.minHeight = 'auto';
        rail.style.transform = 'none';
        stage.scrollLeft = 0;
        updateMobileRailState(0);
      }, 220);
    }, { passive: true });

    window.addEventListener('orientationchange', () => {
      setTimeout(() => {
        mobileLastWidth = window.innerWidth || document.documentElement.clientWidth || 0;
        lastStableVH = 0;
        spacer.style.minHeight = 'auto';
        rail.style.transform = 'none';
        stage.scrollLeft = 0;
        updateMobileRailState(0);
      }, 320);
    }, { passive: true });
  } else {
    /* v2.2.347: scroll work only while portfolio is near the viewport. */
    if ('IntersectionObserver' in window) {
      const portfolioIo = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          portfolioInView = !!entry.isIntersecting;
          if (portfolioInView) requestUpdate();
        });
      }, { rootMargin: '48% 0px 48% 0px', threshold: 0 });
      portfolioIo.observe(spacer);
    }

    /* v1.8.99: NIE uruchamiamy globalnego smooth-wheel, bo blokował scroll strony. */
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        measured = false;
        measure({ force: true });
      }, 120);
    }, { passive: true });
    window.addEventListener('orientationchange', () => {
      setTimeout(() => {
        lastStableVH = 0;
        vhLock = 0;
        measured = false;
        measure({ force: true });
      }, 220);
    }, { passive: true });

    if (document.readyState === 'complete') {
      measure({ force: true });
    } else {
      window.addEventListener('load', () => measure({ force: true }), { once: true });
      measure({ force: true });
    }
  }
})();
</script>

<!-- ZP v2.2.21 — HOME portfolio mobile balanced stability patch: cover images + details + light native rail -->
<style id="zp-home-portfolio-mobile-balanced-v221">@media (max-width:760px){html body #zpShowcaseWhite,html body #zpShowcaseWhite *{-webkit-tap-highlight-color:transparent!important}html body #zpShowcaseWhite{position:relative!important;overflow:clip!important;contain:layout paint!important;background:#fff!important}html body #zpShowcaseWhite .zpShowcaseWhite__spacer,html body #zpShowcaseWhite .zpShowcaseWhite__sticky,html body #zpShowcaseWhite .zpShowcaseWhite__inner{position:relative!important;top:auto!important;left:auto!important;min-height:0!important;height:auto!important;overflow:visible!important;transform:none!important;will-change:auto!important}html body #zpShowcaseWhite .zpShowcaseWhite__head{min-height:0!important;padding-bottom:22px!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__side,html body #zpShowcaseWhite .zpShowcaseWhite__floatingProgress{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile{display:flex!important;width:100%!important;max-width:100%!important;min-height:62px!important;margin:20px 0 0!important;padding:7px!important;border-radius:999px!important;background:#eef1f6!important;border:1px solid rgba(5,7,11,.08)!important;box-shadow:none!important;overflow:hidden!important;contain:layout paint!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchBtn{position:relative!important;z-index:2!important;height:48px!important;min-height:48px!important;padding:0 11px!important;border-radius:999px!important;font-size:15.5px!important;letter-spacing:-.025em!important;font-weight:720!important;background:transparent!important;box-shadow:none!important;outline:0!important}html body #zpShowcaseWhite .zpShowcaseWhite__switchThumb{display:block!important;position:absolute!important;z-index:1!important;top:7px!important;left:7px!important;width:calc(50% - 7px)!important;height:48px!important;border-radius:999px!important;background:#071426!important;transform:translate3d(0,0,0)!important;transition:transform .22s ease!important;will-change:transform!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__switchThumb{transform:translate3d(calc(100% + 0px),0,0)!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint{display:inline-flex!important;position:absolute!important;right:9px!important;bottom:-24px!important;z-index:4!important;width:42px!important;height:22px!important;align-items:center!important;justify-content:center!important;gap:2px!important;opacity:.72!important;color:#071426!important;pointer-events:none!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__tapHint svg{width:15px!important;height:15px!important;display:block!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage{display:block!important;width:100vw!important;margin-left:calc(50% - 50vw)!important;padding:0!important;overflow-x:auto!important;overflow-y:hidden!important;-webkit-overflow-scrolling:touch!important;overscroll-behavior-x:contain!important;overscroll-behavior-y:auto!important;touch-action:pan-x pan-y!important;scroll-snap-type:x proximity!important;scroll-padding-left:18px!important;scroll-behavior:auto!important;contain:layout paint!important;transform:none!important;will-change:auto!important;height:auto!important;min-height:0!important;scrollbar-width:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__stage::-webkit-scrollbar{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__rail{display:flex!important;flex-wrap:nowrap!important;align-items:stretch!important;gap:14px!important;width:max-content!important;min-width:max-content!important;height:auto!important;min-height:0!important;padding:0 18px 10px!important;transform:none!important;transition:none!important;animation:none!important;will-change:auto!important;contain:layout paint!important}html body #zpShowcaseWhite .zpShowcaseWhite__card{position:relative!important;flex:0 0 calc(100vw - 42px)!important;width:calc(100vw - 42px)!important;height:auto!important;min-height:560px!important;max-height:none!important;aspect-ratio:auto!important;border-radius:24px!important;overflow:hidden!important;clip-path:none!important;scroll-snap-align:start!important;scroll-snap-stop:normal!important;background:#05070b!important;color:#fff!important;opacity:1!important;transform:none!important;filter:none!important;transition:none!important;animation:none!important;will-change:auto!important;contain:layout paint!important;box-shadow:0 18px 36px rgba(7,20,38,.14)!important;-webkit-user-select:none!important;user-select:none!important}html body #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{min-height:calc(100vw - 42px)!important;height:calc(100vw - 42px)!important}html body #zpShowcaseWhite .zpShowcaseWhite__media{position:absolute!important;inset:0!important;width:100%!important;height:100%!important;border-radius:inherit!important;overflow:hidden!important;background:#05070b!important;contain:layout paint!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__media img{display:block!important;width:100%!important;height:100%!important;min-width:100%!important;min-height:100%!important;object-fit:cover!important;object-position:center center!important;border-radius:0!important;transform:none!important;filter:none!important;transition:none!important;animation:none!important;will-change:auto!important}html body #zpShowcaseWhite .zpShowcaseWhite__veil{position:absolute!important;inset:0!important;z-index:2!important;background: linear-gradient(180deg,rgba(0,0,0,.35) 0%,rgba(0,0,0,.04) 34%,rgba(0,0,0,.12) 55%,rgba(0,0,0,.84) 100%)!important;opacity:1!important;pointer-events:none!important;transform:none!important;filter:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__top{position:absolute!important;z-index:4!important;top:16px!important;left:16px!important;right:16px!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:12px!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__pill,html body #zpShowcaseWhite .zpShowcaseWhite__index{display:inline-flex!important;align-items:center!important;min-height:30px!important;padding:0 12px!important;border-radius:999px!important;background:rgba(255,255,255,.14)!important;border:1px solid rgba(255,255,255,.18)!important;color:#fff!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;font-size:10px!important;line-height:1!important;letter-spacing:.11em!important;text-transform:uppercase!important;font-weight:760!important}html body #zpShowcaseWhite .zpShowcaseWhite__content{position:absolute!important;z-index:5!important;left:0!important;right:0!important;bottom:0!important;padding:24px 18px 18px!important;color:#fff!important;transform:none!important;filter:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__name{color:#fff!important;font-size:31px!important;line-height:.98!important;letter-spacing:-.05em!important;font-weight:650!important;margin:0!important;max-width:92%!important}html body #zpShowcaseWhite .zpShowcaseWhite__sub{display:block!important;margin-top:8px!important;color:rgba(255,255,255,.76)!important;font-size:13px!important;line-height:1.35!important;letter-spacing:-.01em!important;font-weight:520!important}html body #zpShowcaseWhite .zpShowcaseWhite__meta{display:flex!important;flex-wrap:wrap!important;gap:6px!important;margin:13px 0 0!important;max-height:64px!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseWhite__meta span{display:inline-flex!important;min-height:27px!important;align-items:center!important;padding:0 9px!important;border-radius:999px!important;background:rgba(255,255,255,.11)!important;color:rgba(255,255,255,.86)!important;border:1px solid rgba(255,255,255,.13)!important;font-size:11px!important;line-height:1!important;font-weight:620!important}html body #zpShowcaseWhite .zpShowcaseWhite__actions{display:flex!important;gap:8px!important;margin-top:15px!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__btn{min-height:42px!important;padding:0 13px!important;border-radius:999px!important;font-size:13px!important;line-height:1!important;font-weight:760!important;letter-spacing:-.01em!important;border:1px solid rgba(255,255,255,.22)!important;outline:0!important;box-shadow:none!important;transform:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__btn--primary{background:#fff!important;color:#071426!important}html body #zpShowcaseWhite .zpShowcaseWhite__btn--live{background:rgba(255,255,255,.10)!important;color:#fff!important}html body #zpShowcaseWhite .zpShowcaseWhite__bottom{display:flex!important;padding:18px 18px 0!important;margin:0!important}html body #zpShowcaseWhite .zpShowcaseWhite__hint{display:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__controls{width:100%!important;justify-content:flex-end!important}html body #zpShowcaseWhite .zpShowcaseWhite__navBtn{width:48px!important;height:48px!important;min-width:48px!important;border-radius:999px!important;outline:0!important;box-shadow:none!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseWhite__card--cta{background:#071426!important}html body #zpShowcaseWhite .zpShowcaseWhite__ctaFrame{min-height:100%!important;height:100%!important;display:grid!important;place-items:end start!important;padding:22px!important;border-radius:inherit!important;background:radial-gradient(circle at 80% 10%,rgba(28,71,122,.42),transparent 42%),#071426!important}html body #zpShowcaseWhite .zpShowcaseWhite__ctaTitle{font-size:31px!important;line-height:1!important;letter-spacing:-.045em!important}html body #zpShowcaseWhite .zpShowcaseWhite__card:hover,html body #zpShowcaseWhite .zpShowcaseWhite__card:active,html body #zpShowcaseWhite .zpShowcaseWhite__card:focus-within,html body #zpShowcaseWhite .zpShowcaseWhite__card:hover .zpShowcaseWhite__media img,html body #zpShowcaseWhite .zpShowcaseWhite__card:active .zpShowcaseWhite__media img{transform:none!important;filter:none!important}}</style>
<script id="zp-home-portfolio-mobile-balanced-v221-js">
(function(){
  var root = document.getElementById('zpShowcaseWhite');
  if(!root || root.dataset.zpMobileBalanced221 === '1') return;
  root.dataset.zpMobileBalanced221 = '1';

  function isMobile(){ return (window.innerWidth || document.documentElement.clientWidth || 0) <= 760; }
  function stabilize(){
    if(!isMobile()) return;
    var spacer = root.querySelector('[data-zp-showcase-spacer]');
    var sticky = root.querySelector('[data-zp-showcase-sticky]');
    var stage = root.querySelector('[data-zp-showcase-stage]');
    var rail = root.querySelector('[data-zp-showcase-rail]');
    root.classList.add('is-visible');
    root.classList.remove('is-flowing','is-ending','is-desktop-pin','is-switching');
    root.style.removeProperty('min-height');
    if(spacer){ spacer.style.minHeight = 'auto'; spacer.style.height = 'auto'; }
    if(sticky){ sticky.style.position = 'relative'; sticky.style.top = 'auto'; sticky.style.height = 'auto'; }
    if(stage){
      stage.style.overflowX = 'auto';
      stage.style.overflowY = 'hidden';
      stage.style.transform = 'none';
      stage.style.willChange = 'auto';
      stage.style.scrollBehavior = 'auto';
    }
    if(rail){
      rail.style.transform = 'none';
      rail.style.willChange = 'auto';
      rail.style.transition = 'none';
    }
  }

  stabilize();
  window.addEventListener('load', stabilize, {once:true, passive:true});
  window.addEventListener('orientationchange', function(){ setTimeout(stabilize, 260); }, {passive:true});

  document.addEventListener('click', function(e){
    if(!isMobile()) return;
    var btn = e.target && e.target.closest ? e.target.closest('#zpShowcaseWhite [data-zp-mode-btn]') : null;
    if(btn){ setTimeout(stabilize, 80); }
  }, true);
})();
</script>


<!-- ZP v2.2.22 — HOME portfolio mobile stable rebuild: native rail, cover cards, details, no desktop engine on mobile -->
<style id="zp-home-portfolio-mobile-stable-v222">@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__floatingProgress,html body #zpShowcaseWhite .zpShowcaseWhite__stage,html body #zpShowcaseWhite .zpShowcaseWhite__bottom{display:none!important}html body #zpShowcaseWhite{overflow:hidden!important;background:#fff!important;contain:layout paint!important}html body #zpShowcaseWhite .zpShowcaseWhite__spacer,html body #zpShowcaseWhite .zpShowcaseWhite__sticky,html body #zpShowcaseWhite .zpShowcaseWhite__inner{min-height:0!important;height:auto!important;position:relative!important;top:auto!important;transform:none!important;overflow:visible!important}html body #zpShowcaseWhite .zpShowcaseMobileStable{display:block!important;margin:22px -18px 0!important;position:relative!important;z-index:5!important;contain:layout paint!important}html body #zpShowcaseWhite .zpShowcaseWhite__switch--mobile{display:none!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch{width:calc(100% - 36px)!important;margin:0 auto 16px!important;position:relative!important;display:grid!important;grid-template-columns:1fr 1fr!important;gap:4px!important;padding:5px!important;border-radius:999px!important;background:#eef1f5!important;border:1px solid rgba(7,20,38,.08)!important;box-shadow:0 10px 24px rgba(7,20,38,.06)!important;overflow:visible!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch button{position:relative!important;z-index:2!important;height:42px!important;border:0!important;background:transparent!important;border-radius:999px!important;font-size:13px!important;font-weight:760!important;letter-spacing:-.01em!important;color:#6a7280!important;outline:0!important;box-shadow:none!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch button.is-active{color:#fff!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>i{position:absolute!important;z-index:1!important;top:5px!important;left:5px!important;width:calc(50% - 7px)!important;height:42px!important;border-radius:999px!important;background:linear-gradient(135deg,#071426,#102a4f)!important;box-shadow:0 12px 22px rgba(16,42,79,.20)!important;transform:translateX(0)!important;transition:transform .24s ease!important}html body #zpShowcaseWhite .zpShowcaseMobileStable[data-mode="logo"] .zpShowcaseMobileStable__switch>i{transform:translateX(calc(100% + 4px))!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b{position:absolute!important;right:10px!important;top:-23px!important;display:inline-flex!important;align-items:center!important;gap:4px!important;font-size:10px!important;line-height:1!important;letter-spacing:.12em!important;text-transform:uppercase!important;color:#6a7280!important;font-weight:800!important;opacity:.85!important;animation:zpMobilePortfolioHand 1.25s ease-in-out infinite!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b:before{content:'☝';font-size:15px;line-height:1}@keyframes zpMobilePortfolioHand{0%,100%{transform:translateX(0)}50%{transform:translateX(8px)}}html body #zpShowcaseWhite .zpShowcaseMobileStable__rails{position:relative!important;overflow:hidden!important;contain:layout paint!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__rail{display:flex!important;gap:14px!important;overflow-x:auto!important;overflow-y:hidden!important;overscroll-behavior-x:contain!important;-webkit-overflow-scrolling:touch!important;scrollbar-width:none!important;padding:0 18px 16px!important;contain:layout paint!important;scroll-snap-type:x proximity!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__rail::-webkit-scrollbar{display:none!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__rail[hidden]{display:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__card{position:relative!important;flex:0 0 calc(100vw - 42px)!important;width:calc(100vw - 42px)!important;min-height:560px!important;border-radius:24px!important;overflow:hidden!important;background:#05070b!important;color:#fff!important;scroll-snap-align:start!important;contain:layout paint!important;box-shadow:0 18px 32px rgba(7,20,38,.16)!important}html body #zpShowcaseWhite .zpShowcaseMobile__media{position:absolute!important;inset:0!important;width:100%!important;height:100%!important;background:#05070b!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseMobile__media img{display:block!important;width:100%!important;height:100%!important;min-width:100%!important;min-height:100%!important;object-fit:cover!important;object-position:center center!important;transform:none!important;filter:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__shade{position:absolute!important;inset:0!important;background:linear-gradient(180deg,rgba(0,0,0,.28) 0%,rgba(0,0,0,.02) 36%,rgba(0,0,0,.22) 58%,rgba(0,0,0,.88) 100%)!important;pointer-events:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__top{position:absolute!important;z-index:3!important;top:16px!important;left:16px!important;right:16px!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:12px!important}html body #zpShowcaseWhite .zpShowcaseMobile__top span,html body #zpShowcaseWhite .zpShowcaseMobile__top i{display:inline-flex!important;align-items:center!important;min-height:30px!important;padding:0 12px!important;border-radius:999px!important;background:rgba(255,255,255,.14)!important;border:1px solid rgba(255,255,255,.18)!important;color:#fff!important;font-size:10px!important;letter-spacing:.11em!important;text-transform:uppercase!important;font-weight:760!important;font-style:normal!important}html body #zpShowcaseWhite .zpShowcaseMobile__content{position:absolute!important;z-index:4!important;left:0!important;right:0!important;bottom:0!important;padding:24px 18px 18px!important;color:#fff!important}html body #zpShowcaseWhite .zpShowcaseMobile__content h3{margin:0!important;color:#fff!important;font-size:31px!important;line-height:.98!important;letter-spacing:-.05em!important;font-weight:650!important;max-width:94%!important}html body #zpShowcaseWhite .zpShowcaseMobile__content h3 small{display:block!important;margin-top:8px!important;color:rgba(255,255,255,.76)!important;font-size:13px!important;line-height:1.35!important;letter-spacing:-.01em!important;font-weight:520!important}html body #zpShowcaseWhite .zpShowcaseMobile__meta{display:flex!important;flex-wrap:wrap!important;gap:6px!important;margin:13px 0 0!important;max-height:64px!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseMobile__meta span{display:inline-flex!important;min-height:27px!important;align-items:center!important;padding:0 9px!important;border-radius:999px!important;background:rgba(255,255,255,.11)!important;color:rgba(255,255,255,.86)!important;border:1px solid rgba(255,255,255,.13)!important;font-size:11px!important;line-height:1!important;font-weight:620!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn{margin-top:15px!important;min-height:42px!important;padding:0 16px!important;border-radius:999px!important;border:1px solid rgba(255,255,255,.22)!important;background:#fff!important;color:#071426!important;font-size:13px!important;font-weight:780!important;outline:0!important;box-shadow:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__details{margin-top:12px!important;padding:14px!important;border-radius:18px!important;background:rgba(3,7,18,.72)!important;border:1px solid rgba(255,255,255,.13)!important;color:rgba(255,255,255,.86)!important;max-height:230px!important;overflow:auto!important;-webkit-overflow-scrolling:touch!important}html body #zpShowcaseWhite .zpShowcaseMobile__details p{margin:0 0 10px!important;font-size:12px!important;line-height:1.45!important;color:rgba(255,255,255,.82)!important}html body #zpShowcaseWhite .zpShowcaseMobile__details ul{margin:0!important;padding-left:16px!important;display:grid!important;gap:5px!important}html body #zpShowcaseWhite .zpShowcaseMobile__details li{font-size:12px!important;line-height:1.34!important}html body #zpShowcaseWhite .zpShowcaseMobile__links{display:flex!important;flex-wrap:wrap!important;gap:8px!important;margin-top:12px!important}html body #zpShowcaseWhite .zpShowcaseMobile__links a,html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame a{display:inline-flex!important;align-items:center!important;justify-content:center!important;min-height:40px!important;padding:0 13px!important;border-radius:999px!important;background:#fff!important;color:#071426!important;text-decoration:none!important;font-size:12px!important;font-weight:780!important}html body #zpShowcaseWhite .zpShowcaseMobile__card--cta{display:grid!important;place-items:stretch!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame{min-height:100%!important;padding:24px!important;display:flex!important;flex-direction:column!important;justify-content:flex-end!important;border-radius:inherit!important;background:radial-gradient(circle at 80% 10%,rgba(28,71,122,.42),transparent 42%),#071426!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame span{font-size:10px!important;text-transform:uppercase!important;letter-spacing:.14em!important;color:rgba(255,255,255,.62)!important;font-weight:820!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame h3{margin:10px 0 10px!important;font-size:31px!important;line-height:1!important;letter-spacing:-.045em!important;color:#fff!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame p{margin:0 0 16px!important;font-size:13px!important;line-height:1.45!important;color:rgba(255,255,255,.72)!important}}@media (min-width:761px){html body #zpShowcaseWhite .zpShowcaseMobileStable{display:none!important}}</style>

<!-- ZP v2.2.23 — mobile portfolio viewport card sizing + direct details/live links -->
<style id="zp-home-portfolio-mobile-v223-card-fit">@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseMobileStable{margin:22px 0 0!important;width:100%!important;max-width:100%!important;overflow:visible!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__rails{width:100%!important;max-width:100vw!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__rail{gap:12px!important;padding:0 18px 18px!important;scroll-padding-left:18px!important;scroll-padding-right:18px!important;box-sizing:border-box!important;width:100%!important;max-width:100vw!important}html body #zpShowcaseWhite .zpShowcaseMobile__card{flex:0 0 calc(100vw - 36px)!important;width:calc(100vw - 36px)!important;max-width:calc(100vw - 36px)!important;min-width:calc(100vw - 36px)!important;min-height:min(560px,calc(100svh - 150px))!important;scroll-snap-align:center!important;box-sizing:border-box!important}html body #zpShowcaseWhite .zpShowcaseMobile__content{padding:24px 18px 18px!important;box-sizing:border-box!important}html body #zpShowcaseWhite .zpShowcaseMobile__actions{display:flex!important;align-items:center!important;gap:8px!important;flex-wrap:wrap!important;margin-top:15px!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn,html body #zpShowcaseWhite .zpShowcaseMobile__liveBtn{display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:7px!important;min-height:42px!important;padding:0 15px!important;border-radius:999px!important;text-decoration:none!important;font-size:13px!important;font-weight:780!important;letter-spacing:-.01em!important;line-height:1!important;outline:0!important;box-shadow:none!important;white-space:nowrap!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn{border:1px solid rgba(255,255,255,.22)!important;background:#fff!important;color:#071426!important}html body #zpShowcaseWhite .zpShowcaseMobile__liveBtn{border:1px solid rgba(255,255,255,.26)!important;background:rgba(255,255,255,.12)!important;color:#fff!important;backdrop-filter:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn svg,html body #zpShowcaseWhite .zpShowcaseMobile__liveBtn svg{width:15px!important;height:15px!important;stroke-width:2.2!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch button{display:flex!important;align-items:center!important;justify-content:center!important;gap:7px!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch button svg{width:15px!important;height:15px!important;stroke-width:2.2!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b{right:12px!important;top:-25px!important;gap:5px!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b:before{content:none!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b svg{width:14px!important;height:14px!important;stroke-width:2.35!important}}</style>

<!-- ZP v2.2.24 — portfolio mobile CTA polish + desktop top breathing -->
<style id="zp-home-portfolio-v224-polish">@media (min-width:761px){html body #zpShowcaseWhite .zpShowcaseWhite__inner{padding-top:40px!important}}@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseMobile__actions{display:grid!important;grid-template-columns:1fr 1fr!important;gap:9px!important;align-items:stretch!important;margin-top:15px!important;width:100%!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn,html body #zpShowcaseWhite .zpShowcaseMobile__liveBtn{width:100%!important;min-width:0!important;margin:0!important;height:44px!important;min-height:44px!important;padding:0 10px!important;box-sizing:border-box!important;line-height:1!important;transform:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn span,html body #zpShowcaseWhite .zpShowcaseMobile__liveBtn span{display:inline-flex!important;align-items:center!important;line-height:1!important}html body #zpShowcaseWhite .zpShowcaseMobile__card--cta{background:#060a11!important;border:1px solid rgba(255,255,255,.16)!important;box-shadow:0 18px 34px rgba(7,20,38,.18)!important}html body #zpShowcaseWhite .zpShowcaseMobile__card--cta:before{content:'';position:absolute!important;inset:18px!important;border-radius:24px!important;border:1px solid rgba(255,255,255,.24)!important;pointer-events:none!important;z-index:2!important}html body #zpShowcaseWhite .zpShowcaseMobile__card--cta:after{content:'';position:absolute!important;left:18px!important;right:18px!important;bottom:28px!important;height:4px!important;border-radius:999px!important;background:linear-gradient(90deg,#15375f,#2d68a8)!important;opacity:.9!important;z-index:2!important;pointer-events:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame{position:relative!important;z-index:3!important;min-height:100%!important;padding:46px 30px 58px!important;display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;text-align:center!important;border-radius:inherit!important;background: linear-gradient(180deg,rgba(5,8,14,.15),rgba(5,8,14,.82)),radial-gradient(circle at 72% 18%,rgba(28,71,122,.38),transparent 45%),linear-gradient(135deg,#060a11,#0b1421)!important;overflow:hidden!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame span{display:inline-flex!important;align-items:center!important;gap:9px!important;color:rgba(255,255,255,.64)!important;font-size:11px!important;line-height:1.1!important;text-transform:uppercase!important;letter-spacing:.15em!important;font-weight:820!important;margin-bottom:12px!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame span:before{content:'';width:28px;height:1px;background:#2d68a8;border-radius:99px;display:inline-block}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame h3{margin:0 0 16px!important;max-width:82%!important;color:#fff!important;font-size:clamp(34px,10.4vw,48px)!important;line-height:.95!important;letter-spacing:-.06em!important;font-weight:650!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame p{max-width:84%!important;margin:0 0 28px!important;color:rgba(255,255,255,.68)!important;font-size:14px!important;line-height:1.45!important;letter-spacing:-.015em!important}html body #zpShowcaseWhite .zpShowcaseMobile__ctaFrame a{width:min(100%,330px)!important;min-height:54px!important;border-radius:999px!important;background:#fff!important;color:#071426!important;font-size:15px!important;font-weight:820!important;text-decoration:none!important}}@media (max-width:390px){html body #zpShowcaseWhite .zpShowcaseMobile__actions{gap:7px!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn,html body #zpShowcaseWhite .zpShowcaseMobile__liveBtn{font-size:12px!important;padding:0 8px!important}}</style>

<script id="zp-home-portfolio-mobile-stable-v222-js">
(function(){
  var root=document.querySelector('[data-zp-mobile-stable-portfolio]');
  if(!root || root.dataset.ready==='1') return;
  root.dataset.ready='1';
  var blank="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 10'%3E%3Crect width='16' height='10' fill='%2305070b'/%3E%3C/svg%3E";
  function isMobile(){return (window.innerWidth||document.documentElement.clientWidth||0)<=760;}
  function cards(rail){return rail?[].slice.call(rail.querySelectorAll('[data-zp-mobile-card]')):[];}
  function nearest(rail){
    var list=cards(rail); if(!list.length) return 0;
    var center=(rail.scrollLeft||0)+(rail.clientWidth||0)*.5,best=0,dist=Infinity;
    list.forEach(function(card,i){var c=card.offsetLeft+card.offsetWidth*.5,d=Math.abs(c-center);if(d<dist){dist=d;best=i;}});
    return best;
  }
  function setImg(img,on,priority){
    if(!img) return;
    var real=img.getAttribute('data-zp-mobile-src');
    if(on && real){
      if(img.getAttribute('data-zp-mobile-loaded')!=='1'){
        img.setAttribute('data-zp-mobile-loaded','1');
        try{img.fetchPriority=priority||'low';}catch(e){}
        img.src=real;
      }
    }else if(!on && img.getAttribute('data-zp-mobile-loaded')==='1'){
      img.setAttribute('data-zp-mobile-loaded','0');
      try{img.fetchPriority='low';}catch(e){}
      img.src=blank;
    }
  }
  function hydrateWindow(rail,index){
    if(!rail||!isMobile()) return;
    var list=cards(rail),max=list.length-1;
    index=Math.max(0,Math.min(typeof index==='number'?index:nearest(rail),max));
    list.forEach(function(card,i){
      var img=card.querySelector('img[data-zp-mobile-src]');
      var d=Math.abs(i-index);
      /* At most three decoded portfolio images stay alive on mobile. */
      setImg(img,d<=1,d===0?'high':'low');
    });
  }
  function coolRail(rail){
    if(!rail) return;
    cards(rail).forEach(function(card){setImg(card.querySelector('img[data-zp-mobile-src]'),false,'low');});
  }
  function bindRail(rail){
    if(!rail||rail.dataset.zpPerfBound==='1') return;
    rail.dataset.zpPerfBound='1';
    var raf=0;
    rail.addEventListener('scroll',function(){
      if(raf) return;
      raf=requestAnimationFrame(function(){raf=0;hydrateWindow(rail,nearest(rail));});
    },{passive:true});
    hydrateWindow(rail,0);
  }
  function ensureRail(mode){
    var rail=root.querySelector('[data-zp-mobile-rail="'+mode+'"]');
    if(!rail) return rail;
    if(rail.dataset.zpTemplateReady!=='1'){
      var tpl=root.querySelector('template[data-zp-mobile-template="'+mode+'"]');
      if(tpl && tpl.content){
        rail.appendChild(tpl.content.cloneNode(true));
        rail.dataset.zpTemplateReady='1';
        if(typeof window.zpSuiteHydrateIcons==='function'){
          try{window.zpSuiteHydrateIcons(rail);}catch(e){}
        }
      }
    }
    bindRail(rail);
    return rail;
  }
  function prepareVisibleRail(){
    if(!isMobile()) return;
    var rail=ensureRail(root.getAttribute('data-mode')||'web');
    if(rail) hydrateWindow(rail,nearest(rail));
  }
  function setMode(mode){
    if(mode!=='web' && mode!=='logo') return;
    var activeRail=ensureRail(mode);
    root.setAttribute('data-mode',mode);
    root.querySelectorAll('[data-zp-mobile-mode]').forEach(function(btn){
      var active=btn.getAttribute('data-zp-mobile-mode')===mode;
      btn.classList.toggle('is-active',active);
      btn.setAttribute('aria-selected',active?'true':'false');
    });
    root.querySelectorAll('[data-zp-mobile-rail]').forEach(function(rail){
      var active=rail.getAttribute('data-zp-mobile-rail')===mode;
      rail.hidden=!active;
      rail.classList.toggle('is-active',active);
      if(active){rail.scrollLeft=0;hydrateWindow(rail,0);}else{coolRail(rail);}
    });
    if(activeRail) hydrateWindow(activeRail,0);
  }
  root.addEventListener('click',function(e){
    var modeBtn=e.target.closest&&e.target.closest('[data-zp-mobile-mode]');
    if(modeBtn){e.preventDefault();setMode(modeBtn.getAttribute('data-zp-mobile-mode'));return;}
    var detail=e.target.closest&&e.target.closest('[data-zp-mobile-details]');
    if(detail){
      e.preventDefault();
      var card=detail.closest('[data-zp-mobile-card]');
      var panel=card&&card.querySelector('.zpShowcaseMobile__details');
      if(panel){panel.hidden=!panel.hidden;var label=detail.querySelector('span');if(label)label.textContent=panel.hidden?'Szczegóły':'Ukryj szczegóły';detail.setAttribute('aria-expanded',panel.hidden?'false':'true');}
    }
  },{passive:false});
  if('IntersectionObserver' in window){
    var railObserver=new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){railObserver.disconnect();prepareVisibleRail();}
      });
    },{rootMargin:'320px 0px',threshold:0.01});
    railObserver.observe(root);
  }else if(isMobile()){
    prepareVisibleRail();
  }
  window.addEventListener('resize',prepareVisibleRail,{passive:true});
  window.addEventListener('pagehide',function(){root.querySelectorAll('[data-zp-mobile-rail]').forEach(coolRail);},{passive:true});
})();
</script>


<!-- ZP v2.2.26 — mobile portfolio subtle Lucide hand hint -->
<style id="zp-home-portfolio-mobile-switch-hand-v226">@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseMobileStable__switch{margin-top:28px!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handHint,html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b{position:absolute!important;z-index:9!important;top:-25px!important;left:8px!important;right:auto!important;transform:translateX(0)!important;display:inline-flex!important;align-items:center!important;justify-content:flex-start!important;gap:6px!important;min-height:18px!important;padding:0!important;border-radius:0!important;background:transparent!important;border:0!important;box-shadow:none!important;color:rgba(7,20,38,.48)!important;font-size:10px!important;line-height:1!important;letter-spacing:.14em!important;text-transform:uppercase!important;font-weight:800!important;opacity:1!important;pointer-events:none!important;animation:zpPortfolioSwitchHandHint226 1.35s cubic-bezier(.55,0,.25,1) infinite!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b:before{content:none!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handHint svg,html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b svg{display:block!important;width:15px!important;height:15px!important;stroke-width:2.2!important;color:rgba(7,20,38,.58)!important;opacity:.95!important;flex:0 0 15px!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handHint span,html body #zpShowcaseWhite .zpShowcaseMobileStable__switch>b span{display:inline-block!important}@keyframes zpPortfolioSwitchHandHint226{0%,100%{transform:translateX(0)!important;opacity:.62}42%{transform:translateX(10px)!important;opacity:1}74%{transform:translateX(4px)!important;opacity:.78}}}</style>
<script id="zp-home-portfolio-mobile-switch-hand-v226-js">
(function(){
  function icons(scope){
    try{
      if(typeof window.zpSuiteHydrateIcons==='function'){window.zpSuiteHydrateIcons(scope);return;}
      if(window.lucide&&window.lucide.createIcons){window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}});}
    }catch(e){}
  }
  document.addEventListener('click',function(e){
    var btn=e.target&&e.target.closest&&e.target.closest('#zpShowcaseWhite [data-zp-mobile-mode]');
    if(btn){setTimeout(function(){icons(btn.closest('#zpShowcaseWhite'));},40);}
  },true);
})();
</script>



<!-- ZP v2.2.27 — mobile portfolio inline hand icon guarantee -->
<style id="zp-home-portfolio-mobile-hand-v227">@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handHint{left:8px!important;right:auto!important;top:-25px!important;display:inline-flex!important;align-items:center!important;gap:6px!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handSvg{display:block!important;width:16px!important;height:16px!important;color:rgba(7,20,38,.62)!important;stroke:currentColor!important;flex:0 0 16px!important}}</style>


<!-- ZP v2.2.28 — mobile portfolio hand animation + stronger bottom readability -->
<style id="zp-home-portfolio-mobile-v228-hand-shade">@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handHint{animation:none!important;transform:none!important;opacity:1!important;left:8px!important;right:auto!important;top:-25px!important;display:inline-flex!important;align-items:center!important;gap:6px!important;pointer-events:none!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handHint span{display:inline-block!important;opacity:.72!important}html body #zpShowcaseWhite .zpShowcaseMobileStable__switch .zpShowcaseMobileStable__handSvg{display:block!important;width:17px!important;height:17px!important;flex:0 0 17px!important;color:rgba(7,20,38,.68)!important;stroke:currentColor!important;transform-origin:50% 85%!important;animation:zpMobileSwitchHandOnly228 1.18s cubic-bezier(.5,0,.2,1) infinite!important;will-change:transform!important}@keyframes zpMobileSwitchHandOnly228{0%,100%{transform:translate3d(0,0,0) rotate(-4deg)}42%{transform:translate3d(13px,0,0) rotate(2deg)}70%{transform:translate3d(7px,0,0) rotate(-1deg)}}html body #zpShowcaseWhite .zpShowcaseMobile__shade{background: linear-gradient(180deg,rgba(0,0,0,.20) 0%,rgba(0,0,0,.04) 28%,rgba(0,0,0,.28) 48%,rgba(0,0,0,.78) 72%,rgba(0,0,0,.96) 100% )!important}html body #zpShowcaseWhite .zpShowcaseMobile__content:before{content:''!important;position:absolute!important;z-index:-1!important;left:0!important;right:0!important;bottom:0!important;height:132%!important;background:linear-gradient(180deg,rgba(0,0,0,0),rgba(0,0,0,.70) 43%,rgba(0,0,0,.94) 100%)!important;pointer-events:none!important}html body #zpShowcaseWhite .zpShowcaseMobile__content h3,html body #zpShowcaseWhite .zpShowcaseMobile__content h3 small,html body #zpShowcaseWhite .zpShowcaseMobile__meta,html body #zpShowcaseWhite .zpShowcaseMobile__actions{text-shadow:0 2px 14px rgba(0,0,0,.62)!important}}</style>


<!-- ZP v2.2.34 — HOME portfolio modal web-top + Lucide details fix -->
<style id="zp-home-portfolio-v2234-modal-layout-icons-fix">@media (min-width:761px){html body #zpShowcaseWhite .zpShowcaseWhite__inner{padding-top:85px!important}}@media (max-width:760px){html body #zpShowcaseWhite .zpShowcaseWhite__inner{padding-top:78px!important}}html body .zpShowcasePop,html body .zpShowcasePop.is-open{z-index:2147483000!important}html.zpShowcaseNoScroll body .zpSiteHeader,html.zpShowcaseNoScroll body .zpHeader,html.zpShowcaseNoScroll body .elementor-location-header,html.zpShowcaseNoScroll body header{pointer-events:none!important}html body .zpShowcasePop__backdrop{background:rgba(246,248,251,.84)!important;backdrop-filter:!important;-webkit-backdrop-filter:!important}html body .zpShowcasePop__panel{background:#ffffff!important;color:#071426!important;border:1px solid rgba(7,20,38,.10)!important;box-shadow:0 32px 90px rgba(7,20,38,.18)!important}html body .zpShowcasePop__info{background:linear-gradient(180deg,#ffffff 0%,#f6f8fb 100%)!important;color:#071426!important}html body .zpShowcasePop__pill{background:rgba(7,20,38,.065)!important;color:rgba(7,20,38,.72)!important}html body .zpShowcasePop__title{color:#05070b!important}html body .zpShowcasePop__subtitle,html body .zpShowcasePop__text,html body .zpShowcasePop__value,html body .zpShowcasePop__scope li,html body .zpShowcasePop__tool{color:rgba(7,20,38,.70)!important}html body .zpShowcasePop__label,html body .zpShowcasePop__scopeTitle{color:rgba(7,20,38,.48)!important}html body .zpShowcasePop__grid{background:rgba(7,20,38,.045)!important;border:1px solid rgba(7,20,38,.075)!important}html body .zpShowcasePop__tool{background:rgba(7,20,38,.055)!important;border:1px solid rgba(7,20,38,.065)!important}html body .zpShowcasePop__scope li::before{background:linear-gradient(90deg,#071426,#102a4f,#1c477a)!important}html body .zpShowcasePop__cta a{background:#071426!important;color:#fff!important;border-color:#071426!important}html body .zpShowcasePop__cta a:hover{background:#000!important;color:#fff!important;border-color:#000!important}html body .zpShowcasePop__close{position:absolute!important;top:18px!important;right:18px!important;z-index:2147483001!important;width:48px!important;height:48px!important;min-width:48px!important;min-height:48px!important;padding:0!important;margin:0!important;border-radius:999px!important;border:1px solid rgba(7,20,38,.12)!important;background:#ffffff!important;color:#071426!important;display:flex!important;align-items:center!important;justify-content:center!important;line-height:1!important;text-align:center!important;box-shadow:0 16px 44px rgba(7,20,38,.16)!important;transform:none!important;opacity:1!important;cursor:pointer!important;-webkit-appearance:none!important;appearance:none!important}html body .zpShowcasePop__close:hover,html body .zpShowcasePop__close:focus,html body .zpShowcasePop__close:active{background:#071426!important;color:#ffffff!important;border-color:#071426!important;box-shadow:0 18px 52px rgba(7,20,38,.24)!important;transform:none!important;outline:none!important}html body .zpShowcasePop__close svg{display:block!important;width:20px!important;height:20px!important;flex:0 0 20px!important;margin:0!important;stroke:currentColor!important;stroke-width:2.25!important;transform:none!important}@media (max-width:760px){html body .zpShowcasePop{position:fixed!important;inset:0!important;z-index:2147483000!important}html body .zpShowcasePop__panel{padding-top:0!important;background:#ffffff!important}html body .zpShowcasePop__close{position:fixed!important;top:max(14px,env(safe-area-inset-top))!important;right:14px!important;width:50px!important;height:50px!important;min-width:50px!important;min-height:50px!important}html body .zpShowcasePop__visual{background:#071426!important}html body .zpShowcasePop__info{border-radius:22px 22px 0 0!important;margin-top:-18px!important;position:relative!important;z-index:3!important;padding-top:30px!important}html body .zpShowcasePop__top{padding-right:58px!important}}html body .zpShowcasePop__layout--web{display:grid!important;grid-template-columns:1fr!important;min-height:auto!important}html body .zpShowcasePop__layout--web .zpShowcasePop__visual{min-height:0!important;height:auto!important;background:#071426!important;padding:22px!important}html body .zpShowcasePop__layout--web .zpShowcasePop__visualInner{position:relative!important;inset:auto!important;height:auto!important;padding:0!important;display:block!important}html body .zpShowcasePop__layout--web .zpShowcasePop__visualInner img{width:100%!important;height:auto!important;max-height:min(58vh,620px)!important;aspect-ratio:2560/1707!important;object-fit:cover!important;object-position:center top!important;border-radius:24px!important;box-shadow:0 22px 70px rgba(7,20,38,.22)!important}html body .zpShowcasePop__layout--web .zpShowcasePop__info{padding-top:38px!important}html body .zpShowcasePop__layout--logo{grid-template-columns:minmax(0,1.04fr) minmax(390px,.96fr)!important}html body .zpShowcasePop__pill,html body .zpShowcasePop__label,html body .zpShowcasePop__scopeTitle,html body .zpShowcasePop__tool,html body .zpShowcasePop__scope li{display:inline-flex!important;align-items:center!important;gap:7px!important}html body .zpShowcasePop__label,html body .zpShowcasePop__scopeTitle{display:flex!important}html body .zpShowcasePop__pill [data-lucide],html body .zpShowcasePop__label [data-lucide],html body .zpShowcasePop__scopeTitle [data-lucide],html body .zpShowcasePop__tool [data-lucide],html body .zpShowcasePop__scope li [data-lucide]{width:14px!important;height:14px!important;flex:0 0 14px!important;stroke:currentColor!important;stroke-width:2.1!important}html body .zpShowcasePop__scope li::before{display:none!important;content:none!important}html body .zpShowcasePop__scope li [data-lucide]{margin-top:.15em!important;color:#102a4f!important}html body .zpShowcasePop__scope li span{min-width:0!important}@media (max-width:1180px){html body .zpShowcasePop__layout--logo{grid-template-columns:1fr!important}}@media (max-width:760px){html body .zpShowcasePop__layout--web .zpShowcasePop__visual{padding:12px!important;padding-top:72px!important}html body .zpShowcasePop__layout--web .zpShowcasePop__visualInner img{max-height:none!important;border-radius:18px!important;aspect-ratio:16/10!important}html body .zpShowcasePop__layout--web .zpShowcasePop__info{margin-top:-12px!important}}</style>


<!-- ZP v2.2.699 — home portfolio details use approved Realizacje case-study copy -->
<style id="zp-home-portfolio-realizacje-copy-v2699">
html body .zpShowcasePop__story{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:24px 0}
html body .zpShowcasePop__storyCard{display:grid;grid-template-columns:auto minmax(0,1fr);gap:12px;align-items:start;padding:18px;border:1px solid rgba(7,20,38,.08);border-radius:20px;background:#fff;color:#071426}
html body .zpShowcasePop__storyCard--navy{background:linear-gradient(135deg,#071426,#102a4f);border-color:#102a4f;color:#fff}
html body .zpShowcasePop__storyIcon{width:38px;height:38px;border-radius:999px;display:grid;place-items:center;background:rgba(7,20,38,.065);color:#102a4f}
html body .zpShowcasePop__storyCard--navy .zpShowcasePop__storyIcon{background:rgba(255,255,255,.12);color:#fff}
html body .zpShowcasePop__storyIcon svg{width:17px;height:17px;stroke:currentColor;stroke-width:2}
html body .zpShowcasePop__storyCard small{display:block;margin:1px 0 7px;color:rgba(7,20,38,.46);font-size:10px;line-height:1.1;font-weight:850;letter-spacing:.11em;text-transform:uppercase}
html body .zpShowcasePop__storyCard--navy small{color:rgba(255,255,255,.58)}
html body .zpShowcasePop__storyCard p{margin:0;color:rgba(7,20,38,.74);font-size:13px;line-height:1.58}
html body .zpShowcasePop__storyCard--navy p{color:rgba(255,255,255,.82)}
html body .zpShowcasePop__resultsTitle{display:flex;align-items:center;gap:8px;margin:26px 0 12px;color:rgba(7,20,38,.50);font-size:10px;line-height:1;font-weight:850;letter-spacing:.11em;text-transform:uppercase}
html body .zpShowcasePop__resultsTitle svg{width:15px;height:15px;stroke-width:2.1;color:#102a4f}
html body .zpShowcasePop__results{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:0 0 24px}
html body .zpShowcasePop__result{padding:16px;border-radius:18px;background:rgba(7,20,38,.045);border:1px solid rgba(7,20,38,.075)}
html body .zpShowcasePop__result strong{display:block;margin:0 0 7px;color:#071426;font-size:15px;line-height:1.18;letter-spacing:-.02em}
html body .zpShowcasePop__result span{display:block;color:rgba(7,20,38,.64);font-size:12px;line-height:1.5}
@media(max-width:980px){html body .zpShowcasePop__story,html body .zpShowcasePop__results{grid-template-columns:1fr}}
@media(max-width:760px){html body .zpShowcasePop__story{margin:20px 0}html body .zpShowcasePop__storyCard{padding:15px;border-radius:17px}html body .zpShowcasePop__results{gap:8px}html body .zpShowcasePop__result{padding:14px;border-radius:16px}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn{appearance:none;-webkit-appearance:none;display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:7px!important}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn[aria-expanded="true"] svg{transform:rotate(180deg)}html body #zpShowcaseWhite .zpShowcaseMobile__detailsBtn svg{transition:transform .2s ease}}
</style>

<!-- ZP v2.2.700 — dynamically hydrated desktop modal icon guarantee -->
<style id="zp-home-portfolio-modal-icons-v2700">
html body .zpShowcasePop__pill svg.lucide,
html body .zpShowcasePop__label svg.lucide,
html body .zpShowcasePop__scopeTitle svg.lucide,
html body .zpShowcasePop__tool svg.lucide,
html body .zpShowcasePop__scope li svg.lucide{display:block!important;width:14px!important;height:14px!important;min-width:14px!important;flex:0 0 14px!important;stroke:currentColor!important;stroke-width:2.1!important;overflow:visible!important}
html body .zpShowcasePop__scope li svg.lucide{margin-top:.15em!important;color:#102a4f!important}
html body .zpShowcasePop__storyIcon svg.lucide{display:block!important;width:17px!important;height:17px!important;stroke:currentColor!important;overflow:visible!important}
html body .zpShowcasePop__resultsTitle svg.lucide{display:block!important;width:15px!important;height:15px!important;flex:0 0 15px!important;color:#102a4f!important;stroke:currentColor!important;overflow:visible!important}
</style>

    <?php
    return ob_get_clean();
  }

  add_shortcode('zp_showcase_portfolio', 'zp_showcase_portfolio_shortcode');
}
