<?php
$zp_home_faq_items = array_values(array_filter(zp_suite_cms_get('faq', []), function($r){
  return !isset($r['visible']) || (string)$r['visible'] !== '0';
}));
// 2.9.4: questions and answers written for Google and AI assistants (includes/home-294.php).
if (function_exists('zp_home_faq_294_items')) {
  $zp_home_faq_items = zp_home_faq_294_items($zp_home_faq_items);
}
if (empty($zp_home_faq_items)) {
  return;
}
?>
<!-- =========================================================
ZAPROJEKTOWANI — HOME FAQ / KATOWICE NAVY STYLE v2.2.190
Wygląd 1:1 z /strony-internetowe-katowice/ + treść FAQ z CMS home.
========================================================= -->
<section class="zpFaqKatNavy zpHomeFaqKatNavy" id="zpHomeFaqKatNavy" aria-labelledby="zpHomeFaqKatNavyTitle" itemscope itemtype="https://schema.org/FAQPage">
  <style>.zpHomeFaqKatNavy,.zpHomeFaqKatNavy *,.zpHomeFaqKatNavy *::before,.zpHomeFaqKatNavy *::after{box-sizing:border-box}.zpHomeFaqKatNavy{--font:"Plus Jakarta Sans Local";--ink:#05070b;--ink2:#07111f;--muted:#5c6675;--muted2:#7a8493;--line:rgba(7,17,31,.10);--line2:rgba(7,17,31,.15);--soft:#f6f7f9;--panel:#fff;--navy:#071426;--navy2:#0b1830;--navy3:#102a4f;--blue:#1c477a;--ease:cubic-bezier(.22,1,.36,1);--navyGrad:linear-gradient(135deg,#030407 0%,#071426 34%,#102a4f 72%,#1c477a 100%);--btnGrad:linear-gradient(90deg,#05070b,#0b1830,#102a4f,#1c477a);position:relative;width:100%;max-width:100%;margin:0;padding:clamp(74px,7vw,126px) 0 clamp(62px,6.4vw,110px);isolation:isolate;overflow:hidden;color:var(--ink);background:#fff;font-family:var(--font);-webkit-font-smoothing:antialiased;text-rendering:geometricPrecision;font-synthesis:none}.zpHomeFaqKatNavy *,.zpHomeFaqKatNavy button{font-family:var(--font)!important}.zpHomeFaqKatNavy__bleed{position:absolute;z-index:-6;inset:0 calc(50% - 50vw);pointer-events:none;background:#fff!important}.zpHomeFaqKatNavy__bleed::before,.zpHomeFaqKatNavy__bleed::after{display:none!important;content:none!important;background:none!important}.zpHomeFaqKatNavy::before,.zpHomeFaqKatNavy::after{display:none!important;content:none!important;background:none!important}.zpHomeFaqKatNavy__watermark{position:absolute;z-index:-1;left:50%;bottom:-.07em;width:100vw;transform:translateX(-50%);pointer-events:none;user-select:none;color:#05070b;opacity:.028;font-size:clamp(108px,15vw,260px);line-height:.78;letter-spacing:-.105em;font-weight:760;white-space:nowrap;text-align:center;text-transform:lowercase;-webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,#000 0%,#000 54%,rgba(0,0,0,.66) 74%,transparent 100%);mask-image:linear-gradient(90deg,transparent 0%,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,#000 0%,#000 54%,rgba(0,0,0,.66) 74%,transparent 100%);-webkit-mask-composite:source-in;mask-composite:intersect}.zpHomeFaqKatNavy a{color:inherit;text-decoration:none!important}.zpHomeFaqKatNavy svg{display:block}.zpHomeFaqKatNavy__inner{position:relative;z-index:3;width:min(1740px,calc(100% - clamp(28px,6vw,112px)));margin:0 auto}.zpHomeFaqKatNavy__head{display:grid;grid-template-columns:minmax(0,1fr) minmax(320px,560px);gap:clamp(28px,5vw,92px);align-items:end;margin-bottom:clamp(36px,5vw,76px)}.zpHomeFaqKatNavy__kicker{display:inline-flex;align-items:center;gap:10px;margin:0 0 15px;color:#667386;text-transform:uppercase;letter-spacing:.13em;font-size:10px;line-height:1;font-weight:850}.zpHomeFaqKatNavy__kicker::before{content:"";width:24px;height:1px;background:#07111f}.zpHomeFaqKatNavy h2{max-width:1080px;margin:0;color:var(--ink);font-size:clamp(36px,5.15vw,76px);line-height:.98;letter-spacing:-.052em;font-weight:620;text-wrap:balance}.zpHomeFaqKatNavy__lead{margin:0;max-width:560px;color:var(--muted);font-size:clamp(14px,1.04vw,17px);line-height:1.62;letter-spacing:-.01em}.zpHomeFaqKatNavy__layout{display:grid;grid-template-columns:minmax(320px,460px) minmax(0,1fr);gap:clamp(18px,2vw,30px);align-items:start}.zpHomeFaqKatNavy__side{position:sticky;top:112px}.zpHomeFaqKatNavy__sideCard{position:relative;overflow:hidden;padding:clamp(24px,2.2vw,34px);border-radius:34px;background:#fff!important;border:1px solid rgba(7,17,31,.11);box-shadow:none}.zpHomeFaqKatNavy__sideCard::before{display:none!important;content:none!important;background:none!important}.zpHomeFaqKatNavy__sideLabel{position:relative;display:inline-flex;align-items:center;min-height:28px;padding:0 11px;border-radius:999px;background:#f5f5f5!important;color:#344256;font-size:9px;line-height:1;letter-spacing:.12em;font-weight:900;text-transform:uppercase}.zpHomeFaqKatNavy__sideCard h3{position:relative;margin:26px 0 0;color:#05070b;font-size:clamp(28px,2.15vw,40px);line-height:1;letter-spacing:-.055em;font-weight:620;text-wrap:balance}.zpHomeFaqKatNavy__sideCard p{position:relative;margin:20px 0 0;color:#5c6675;font-size:14px;line-height:1.62;letter-spacing:-.006em}.zpHomeFaqKatNavy__chips{position:relative;display:flex;flex-wrap:wrap;gap:8px;margin-top:26px}.zpHomeFaqKatNavy__chips span{display:inline-flex;align-items:center;gap:7px;min-height:34px;padding:0 11px;border-radius:999px;background:#f5f5f5!important;border:1px solid rgba(7,17,31,.08);color:#344256;font-size:10px;line-height:1;letter-spacing:.04em;font-weight:800}.zpHomeFaqKatNavy__chips svg{width:14px;height:14px;color:#071426;stroke-width:2.2}.zpHomeFaqKatNavy__sideCta{position:relative;z-index:1;min-height:52px;display:inline-flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:28px;padding:0 22px;border-radius:999px;overflow:hidden;background:#fff;color:#07111f!important;border:1px solid rgba(7,17,31,.16);font-size:11px;line-height:1;letter-spacing:.07em;font-weight:850;text-transform:none;white-space:nowrap;transition:transform .34s var(--ease),border-color .34s var(--ease),color .34s var(--ease),background .34s var(--ease)}.zpHomeFaqKatNavy__sideCta::before{content:"";position:absolute;inset:0;z-index:-1;background:var(--btnGrad);transform:scaleX(0);transform-origin:left;transition:transform .44s var(--ease)}.zpHomeFaqKatNavy__sideCta:hover{color:#fff!important;background:#071426;border-color:rgba(16,42,79,.65);transform:translateY(-2px)}.zpHomeFaqKatNavy__sideCta:hover::before{transform:scaleX(1)}.zpHomeFaqKatNavy__sideCta svg{width:15px;height:15px;stroke-width:2;transition:transform .34s var(--ease)}.zpHomeFaqKatNavy__sideCta:hover svg{transform:translate(2px,-2px)}.zpHomeFaqKatNavy__items{display:grid;gap:10px}.zpHomeFaqKatNavy__item{position:relative;overflow:hidden;border-radius:28px!important;background:#fff!important;border:1px solid rgba(7,17,31,.105);box-shadow:none;transition:border-color .28s ease,background .28s ease,transform .28s var(--ease)}.zpHomeFaqKatNavy__item:hover,.zpHomeFaqKatNavy__item.is-open{border-color:rgba(7,20,38,.28);background:#fff!important}.zpHomeFaqKatNavy__question{appearance:none;-webkit-appearance:none;width:100%;display:grid;grid-template-columns:44px minmax(0,1fr) 42px;gap:14px;align-items:center;border:0!important;background:#fff!important;padding:20px;cursor:pointer;color:var(--ink)!important;text-align:left;border-radius:28px!important;min-width:0!important;overflow:visible!important;transition:background .34s var(--ease),color .34s ease}.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question{border-radius:28px 28px 0 0!important}.zpHomeFaqKatNavy__item:hover .zpHomeFaqKatNavy__question,.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question{background:var(--navyGrad)!important;color:#fff!important}.zpHomeFaqKatNavy__question > span{width:44px;height:44px;display:grid;place-items:center;border-radius:16px;background:#f5f5f5!important;color:#344256!important;font-size:11px;line-height:1;letter-spacing:-.04em;font-weight:850;transition:background .34s var(--ease),color .34s ease}.zpHomeFaqKatNavy__item:hover .zpHomeFaqKatNavy__question > span,.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question > span{background:#fff!important;color:#071426!important}.zpHomeFaqKatNavy__question strong{display:block!important;min-width:0!important;max-width:100%!important;white-space:normal!important;overflow:visible!important;text-overflow:unset!important;overflow-wrap:anywhere!important;word-break:normal!important;color:#05070b!important;font-size:clamp(18px,1.25vw,23px);line-height:1.1;letter-spacing:-.038em;font-weight:640;transition:color .34s ease}.zpHomeFaqKatNavy__item:hover .zpHomeFaqKatNavy__question strong,.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question strong{color:#fff!important}.zpHomeFaqKatNavy__question > svg{justify-self:end;width:42px;height:42px;display:grid;place-items:center;border-radius:999px;background:#f5f5f5!important;color:#07111f!important;padding:12px;transition:transform .34s var(--ease),background .28s ease,color .28s ease}.zpHomeFaqKatNavy__item:hover .zpHomeFaqKatNavy__question > svg,.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question > svg{background:#fff!important;color:#071426!important}.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question > svg{transform:rotate(45deg)}.zpHomeFaqKatNavy__answer{max-height:0;overflow:hidden;background:#fff!important;border-radius:0 0 28px 28px!important;transition:max-height .46s var(--ease)}.zpHomeFaqKatNavy__answer p{margin:0;padding:22px 76px 24px 78px;color:#5c6675;font-size:14.5px;line-height:1.66;letter-spacing:-.006em}.zpHomeFaqKatNavy__answer strong{color:#07111f;font-weight:760}.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__answer{max-height:var(--zpFaqAnswerH,620px)}.zpHomeFaqKatNavy__copy,.zpHomeFaqKatNavy__lead,.zpHomeFaqKatNavy__side,.zpHomeFaqKatNavy__items{opacity:0;transform:translate3d(0,34px,0);filter:none;transition:opacity .86s var(--ease),transform .96s var(--ease),filter .86s ease}.zpHomeFaqKatNavy.is-inview .zpHomeFaqKatNavy__copy,.zpHomeFaqKatNavy.is-inview .zpHomeFaqKatNavy__lead,.zpHomeFaqKatNavy.is-inview .zpHomeFaqKatNavy__side,.zpHomeFaqKatNavy.is-inview .zpHomeFaqKatNavy__items{opacity:1;transform:translate3d(0,0,0);filter:none}.zpHomeFaqKatNavy.is-inview .zpHomeFaqKatNavy__lead{transition-delay:.10s}.zpHomeFaqKatNavy.is-inview .zpHomeFaqKatNavy__side{transition-delay:.16s}.zpHomeFaqKatNavy.is-inview .zpHomeFaqKatNavy__items{transition-delay:.22s}@media(max-width:1180px){.zpHomeFaqKatNavy__head{grid-template-columns:1fr}.zpHomeFaqKatNavy__lead{max-width:760px}.zpHomeFaqKatNavy__layout{grid-template-columns:1fr}.zpHomeFaqKatNavy__side{position:relative;top:auto}}@media(max-width:760px){.zpHomeFaqKatNavy{padding:56px 0 64px}.zpHomeFaqKatNavy__inner{width:calc(100% - 20px)}.zpHomeFaqKatNavy__head{gap:16px;margin-bottom:26px}.zpHomeFaqKatNavy__layout{display:flex!important;flex-direction:column!important}.zpHomeFaqKatNavy__items{order:1!important}.zpHomeFaqKatNavy__side{order:2!important;width:100%!important}.zpHomeFaqKatNavy h2{font-size:clamp(32px,9.2vw,44px);line-height:1;letter-spacing:-.052em}.zpHomeFaqKatNavy__lead{max-width:100%;font-size:13px;line-height:1.58}.zpHomeFaqKatNavy__sideCard{border-radius:24px;padding:22px}.zpHomeFaqKatNavy__sideCard h3{font-size:clamp(28px,8vw,36px);line-height:1}.zpHomeFaqKatNavy__sideCard p{font-size:13px;line-height:1.56}.zpHomeFaqKatNavy__chips{gap:7px}.zpHomeFaqKatNavy__chips span{min-height:32px;font-size:9.5px}.zpHomeFaqKatNavy__item{border-radius:22px!important}.zpHomeFaqKatNavy__question{grid-template-columns:36px minmax(0,1fr) 38px!important;gap:12px;padding:16px!important;border-radius:22px!important}.zpHomeFaqKatNavy__answer{border-radius:0 0 22px 22px!important}.zpHomeFaqKatNavy__question > span{width:36px!important;height:36px!important;border-radius:13px!important;font-size:10px!important}.zpHomeFaqKatNavy__question strong{font-size:17px!important;line-height:1.16!important;white-space:normal!important;overflow-wrap:anywhere!important;text-wrap:pretty}.zpHomeFaqKatNavy__question > svg{width:38px!important;height:38px!important;padding:11px!important}.zpHomeFaqKatNavy__answer p{padding:18px 16px 18px 64px;font-size:13px;line-height:1.58}.zpHomeFaqKatNavy__watermark{font-size:clamp(76px,22vw,140px);opacity:.026;bottom:.02em}}@media(max-width:430px){.zpHomeFaqKatNavy__question{grid-template-columns:32px minmax(0,1fr) 36px!important;gap:10px!important;padding:15px!important}.zpHomeFaqKatNavy__question > span{display:grid!important;width:32px!important;height:32px!important;border-radius:12px!important;font-size:9px!important}.zpHomeFaqKatNavy__question > svg{width:36px!important;height:36px!important;padding:10px!important}.zpHomeFaqKatNavy__answer p{padding:18px 15px 18px!important}}.zpHomeFaqKatNavy__item:hover{background:var(--navyGrad)!important;border-color:rgba(7,20,38,.30)!important}.zpHomeFaqKatNavy__item:hover .zpHomeFaqKatNavy__question{border-radius:28px!important;background:var(--navyGrad)!important}.zpHomeFaqKatNavy__item.is-open{background:#fff!important}.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question{background:var(--navyGrad)!important}.zpHomeFaqKatNavy__sideCta{text-transform:none!important;letter-spacing:.015em!important;font-size:12px!important}@media(max-width:760px){.zpHomeFaqKatNavy__item:hover .zpHomeFaqKatNavy__question{border-radius:22px!important}.zpHomeFaqKatNavy__item.is-open .zpHomeFaqKatNavy__question{border-radius:22px 22px 0 0!important}}@media(prefers-reduced-motion:reduce){.zpHomeFaqKatNavy *,.zpHomeFaqKatNavy *::before,.zpHomeFaqKatNavy *::after{animation:none!important;transition:none!important;scroll-behavior:auto!important}.zpHomeFaqKatNavy__copy,.zpHomeFaqKatNavy__lead,.zpHomeFaqKatNavy__side,.zpHomeFaqKatNavy__items{opacity:1!important;transform:none!important;filter:none!important}}</style>
  <?php
  // 2.9.8 (Mat 8.10: "daj też zdjęcie Mateusza z boku"). On computers Mateusz sits on the card at the top of the
  // column; the card alone follows the questions, as before (sticky at 112 px), and the photo's bottom fades so it
  // leaves the card softly. Tablets and phones keep the card alone.
  ?>
  <style id="zp-home-faq-person">.zpHomeFaqKatNavy__person{display:none}@media (min-width:1181px){.zpHomeFaqKatNavy__side{position:relative;top:auto;align-self:stretch}.zpHomeFaqKatNavy__person{position:relative;z-index:0;display:block;width:max-content;height:340px;margin:0 clamp(26px,2.6vw,46px) -24px auto;pointer-events:none}.zpHomeFaqKatNavy__person img{display:block;width:auto;height:100%;max-width:none;-webkit-mask-image:linear-gradient(to bottom,#000 88%,transparent 100%);mask-image:linear-gradient(to bottom,#000 88%,transparent 100%)}.zpHomeFaqKatNavy__sideCard{position:sticky;top:112px;z-index:1}}</style>

  <div class="zpHomeFaqKatNavy__bleed" aria-hidden="true"></div>
  <div class="zpHomeFaqKatNavy__watermark" aria-hidden="true">faq</div>

  <div class="zpHomeFaqKatNavy__inner">
    <header class="zpHomeFaqKatNavy__head">
      <div class="zpHomeFaqKatNavy__copy">
        <span class="zpHomeFaqKatNavy__kicker">FAQ / Zaprojektowani</span>
        <h2 id="zpHomeFaqKatNavyTitle">Najczęstsze pytania o strony internetowe, sklepy, logo i kampanie.</h2>
      </div>
      <p class="zpHomeFaqKatNavy__lead">W skrócie: strona internetowa od 3 999 zł z domeną i hostingiem w cenie, sklep WooCommerce od 6 499 zł, logo od 999 zł, a obsługa kampanii od 1 200 zł miesięcznie. Poniżej odpowiadamy na pytania, które słyszymy przed każdym projektem.</p>
    </header>

    <div class="zpHomeFaqKatNavy__layout">
      <aside class="zpHomeFaqKatNavy__side" aria-label="Informacje o współpracy">
        <?php // 2.9.8 (Mat 8.10): Mateusz sits on the card beside the questions, on computers only (styles below). ?>
        <figure class="zpHomeFaqKatNavy__person" aria-hidden="true"><img src="<?php echo esc_url(ZP_SUITE_URL . 'assets/img/home/mateusz-faq-280.webp'); ?>" srcset="<?php echo esc_url(ZP_SUITE_URL . 'assets/img/home/mateusz-faq-280.webp'); ?> 280w, <?php echo esc_url(ZP_SUITE_URL . 'assets/img/home/mateusz-faq-560.webp'); ?> 560w" sizes="279px" alt="" width="280" height="342" loading="lazy" decoding="async"></figure>
        <div class="zpHomeFaqKatNavy__sideCard">
          <span class="zpHomeFaqKatNavy__sideLabel">Przed startem projektu</span>
          <h3>Najpierw porządkujemy zakres, potem projektujemy rozwiązanie.</h3>
          <p>FAQ pomaga szybko sprawdzić, jak wygląda współpraca, co warto przygotować i kiedy wybrać stronę, sklep WooCommerce, logo albo kampanię reklamową.</p>
          <div class="zpHomeFaqKatNavy__chips" aria-label="Zakres FAQ">
            <span><i data-lucide="layout-template"></i> strony www</span>
            <span><i data-lucide="shopping-bag"></i> sklepy</span>
            <span><i data-lucide="sparkles"></i> branding</span>
            <span><i data-lucide="megaphone"></i> reklamy</span>
          </div>
          <a class="zpHomeFaqKatNavy__sideCta" href="/studio-wyceny/">
            <span>Przejdź do wyceny</span>
            <i data-lucide="arrow-up-right"></i>
          </a>
        </div>
      </aside>

      <div class="zpHomeFaqKatNavy__items" data-zp-home-faq-kat-navy-items>
        <?php foreach ($zp_home_faq_items as $i => $faq) :
          $q = trim((string)($faq['q'] ?? ''));
          $a = trim((string)($faq['a'] ?? ''));
          if ($q === '' || $a === '') { continue; }
        ?>
          <article class="zpHomeFaqKatNavy__item<?php echo $i === 0 ? ' is-open' : ''; ?>" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <button class="zpHomeFaqKatNavy__question" type="button" aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>">
              <span><?php echo esc_html(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <strong itemprop="name"><?php echo esc_html($q); ?></strong>
              <i data-lucide="plus"></i>
            </button>
            <div class="zpHomeFaqKatNavy__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
              <p itemprop="text"><?php echo !empty($faq['html']) ? wp_kses($a, ['a' => ['href' => true, 'class' => true, 'data-zp-local' => true], 'strong' => []]) : esc_html($a); ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <script>
    (function(){
      var root = document.getElementById('zpHomeFaqKatNavy');
      if(!root || root.dataset.ready === '1') return;
      root.dataset.ready = '1';
      var items = Array.prototype.slice.call(root.querySelectorAll('.zpHomeFaqKatNavy__item'));
      function syncHeights(){
        items.forEach(function(item){
          var answer = item.querySelector('.zpHomeFaqKatNavy__answer');
          if(answer){ item.style.setProperty('--zpFaqAnswerH', (answer.scrollHeight + 8) + 'px'); }
        });
      }
      function openItem(target){
        items.forEach(function(item){
          var isTarget = item === target;
          item.classList.toggle('is-open', isTarget);
          var btn = item.querySelector('.zpHomeFaqKatNavy__question');
          if(btn) btn.setAttribute('aria-expanded', isTarget ? 'true' : 'false');
        });
        syncHeights();
      }
      items.forEach(function(item){
        var btn = item.querySelector('.zpHomeFaqKatNavy__question');
        if(btn){ btn.addEventListener('click', function(){ openItem(item); }); }
      });
      syncHeights();
      window.addEventListener('resize', syncHeights, {passive:true});
      if('IntersectionObserver' in window){
        var io = new IntersectionObserver(function(entries){
          entries.forEach(function(entry){ if(entry.isIntersecting){ root.classList.add('is-inview'); io.disconnect(); } });
        }, {threshold:.12});
        io.observe(root);
      } else { root.classList.add('is-inview'); }
      if(window.lucide && typeof window.lucide.createIcons === 'function') window.lucide.createIcons();
    })();
  </script>
</section>
<!-- /ZAPROJEKTOWANI — HOME FAQ / KATOWICE NAVY STYLE v2.2.190 -->
