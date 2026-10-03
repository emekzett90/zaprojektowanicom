<?php
/**
 * Full-width route template for /kampanie-reklamowe/.
 * Uses the original Zaprojektowani Suite header and footer.
 */
if (!defined('ABSPATH')) { exit; }

$zp_campaign_asset_url = ZP_SUITE_URL . 'assets/campaigns/';
$zp_campaign_css_file  = ZP_SUITE_PATH . 'assets/campaigns/campaigns.css';
$zp_campaign_js_file   = ZP_SUITE_PATH . 'assets/campaigns/campaigns.js';
$zp_campaign_css_ver   = file_exists($zp_campaign_css_file) ? (string) filemtime($zp_campaign_css_file) : ZP_SUITE_VERSION;
$zp_campaign_js_ver    = file_exists($zp_campaign_js_file) ? (string) filemtime($zp_campaign_js_file) : ZP_SUITE_VERSION;

/* Load the exact same original navigation/footer assets as on the other pages. */
if (function_exists('zp_suite_enqueue_block_assets')) {
  zp_suite_enqueue_block_assets('zp_header');
  zp_suite_enqueue_block_assets('zp_footer');
  /* v2.2.688: wspólny formularz kontaktowy pod FAQ. */
  zp_suite_enqueue_block_assets('zp_contact_system');
}
if (function_exists('zp_suite_enqueue_global_assets')) {
  zp_suite_enqueue_global_assets();
}

wp_enqueue_style(
  'zp-suite-campaigns-page',
  $zp_campaign_asset_url . 'campaigns.css',
  [],
  $zp_campaign_css_ver
);
wp_enqueue_script(
  'zp-suite-campaigns-page',
  $zp_campaign_asset_url . 'campaigns.js',
  [],
  $zp_campaign_js_ver,
  true
);

status_header(200);

/* 2.4.0: with the SEO plan on, this is the real WordPress page: the title, description and
   canonical come from Rank Math and the page cache may keep it like any other page. */
$zp_campaign_rank_math = function_exists('zp_seo_plan_active') && zp_seo_plan_active() && is_page();
if ($zp_campaign_rank_math) {
  // The page's own body classes (page, page-id-…) would switch on site-wide rules that this
  // template was never styled for, so the body keeps the classes it had as a virtual route.
  add_filter('body_class', static function ($classes) {
    return array_values(array_filter((array) $classes, static function ($c) {
      return !preg_match('~^(page|page-template.*|page-id-\d+|page-parent|page-child|parent-pageid-\d+|elementor-page(-\d+)?|singular|wp-singular)$~', (string) $c);
    }));
  }, PHP_INT_MAX);
}
if (!$zp_campaign_rank_math) {
  nocache_headers();
  add_filter('pre_get_document_title', static function(){
    return 'Kampanie reklamowe Katowice — Meta Ads + Google Ads | Zaprojektowani';
  }, 999);
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#05070b">
<?php if (!$zp_campaign_rank_math) : ?>
  <meta name="description" content="Kampanie reklamowe Katowice — Meta Ads i Google Ads. Strategia, kreacja, analityka i optymalizacja kampanii w jednym zespole.">
  <link rel="canonical" href="<?php echo esc_url(home_url('/kampanie-reklamowe/')); ?>">
<?php endif; ?>
  <link rel="preload" href="<?php echo esc_url($zp_campaign_asset_url); ?>plus-jakarta-sans-400.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?php echo esc_url($zp_campaign_asset_url); ?>plus-jakarta-sans-700.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?php echo esc_url($zp_campaign_asset_url); ?>team-hero.webp" as="image" type="image/webp" fetchpriority="high">
  <?php wp_head(); ?>
</head>
<body <?php body_class('zp-campaigns-page zp-service-hero-page'); ?>>
<?php wp_body_open(); ?>
<?php
/* v2.2.695: ta trasa renderuje się na parse_request i pomija bufor
   optymalizatora obrazów (template_redirect), który na pozostałych
   podstronach podmienia przyciętą miniaturę 150×150 sygnetu na pełny plik.
   Bez tego logo w headerze i stopce jest ucięte. Podmieniamy je tutaj. */
$zp_logo_thumb_fix = static function ($html) {
  return str_replace(
    ['zp_sygnet-150x150.webp', 'zp_sygnet_ciemny-150x150.webp'],
    ['zp_sygnet.webp', 'zp_sygnet_ciemny.webp'],
    (string) $html
  );
};
echo $zp_logo_thumb_fix(do_shortcode('[zp_header]'));
?>

<div class="zpCampaignPage" id="zpCampaignPage">
  <div class="scroll-progress" aria-hidden="true"></div>
  <main>
    <section class="hero" id="start">
      <div class="hero-noise" aria-hidden="true"></div>
      <div class="hero-orbit" aria-hidden="true"><i></i></div>
      <img class="hero-watermark" src="<?php echo esc_url($zp_campaign_asset_url); ?>zp-sygnet-ciemny.webp" alt="" aria-hidden="true">
      <div class="hero-inner">
        <div class="hero-copy">
          <p class="hero-eyebrow">Kampanie reklamowe • Katowice</p>
          <h1><span class="sr-only">Kampanie reklamowe dla firm — </span>Meta Ads + Google Ads, które mają <strong class="gradient-text">konkretny cel.</strong></h1>
          <p class="hero-lead">Łączymy <strong>strategię, kreację, analitykę i optymalizację</strong>, żeby reklama nie kończyła się na kliknięciu. Budujemy spójną drogę: od pierwszego kontaktu z marką do telefonu, formularza, rezerwacji albo zakupu.</p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="#pakiety">Dobierz zakres kampanii <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
            <a class="btn btn-ghost" href="#proces">Zobacz, jak pracujemy <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></a>
          </div>
          <div class="hero-proof" aria-label="Zakres działań">
            <span class="proof-pill"><i></i> strategia kampanii</span>
            <span class="proof-pill"><i></i> kreacje i copy</span>
            <span class="proof-pill"><i></i> pomiar konwersji</span>
          </div>
        </div>
      </div>

      <div class="hero-stage" aria-label="Zespół Zaprojektowani oraz elementy kampanii Meta Ads i Google Ads">
        <img class="hero-team" src="<?php echo esc_url($zp_campaign_asset_url); ?>team-hero.webp" alt="Zespół Zaprojektowani — kampanie Meta Ads i Google Ads" decoding="async" fetchpriority="high">
        <div class="meta-float platform-float"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="Meta Ads" decoding="async"></div>
        <div class="google-float platform-float"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="Google Ads" decoding="async"></div>
        <div class="hero-dashboard" aria-label="Podgląd trendu skuteczności kampanii">
          <div class="dash-top"><span class="dash-label">Kokpit kampanii</span><span class="live-pill"><i></i> live</span></div>
          <div class="dash-title"><strong>ROAS 7,23×</strong><span>Meta + Google • 90 dni</span></div>
          <svg class="mini-chart" viewBox="0 0 420 90" role="img" aria-label="Rosnący trend działań po optymalizacji kampanii">
            <defs><linearGradient id="heroArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8ec8f7" stop-opacity=".25"/><stop offset="1" stop-color="#8ec8f7" stop-opacity="0"/></linearGradient></defs>
            <path class="grid" d="M0 20h420M0 45h420M0 70h420"/>
            <path class="area" d="M0 73C42 71 55 58 88 63s51-3 76-18 51 12 77 3 50-37 80-23 48-10 99-20v85H0z"/>
            <path class="line" d="M0 73C42 71 55 58 88 63s51-3 76-18 51 12 77 3 50-37 80-23 48-10 99-20"/>
          </svg>
          <div class="dash-foot"><span><i></i> Google Ads</span><span><i></i> Meta Ads</span><span>▲ +39% kw/kw</span></div>
        </div>
      </div>

      <div class="zpServiceHeroScroll" aria-hidden="true">
        <span class="zpServiceHeroScroll__mouse"></span>
        <span>Przewiń</span>
      </div>
      <div class="hero-rail" aria-hidden="true">
        <div class="hero-rail__inner">
          <div class="rail-stat"><strong data-count="7.23" data-decimals="2" data-suffix="×">7,23×</strong><span>łączny ROAS klientów • 90 dni</span></div><i class="rail-sep"></i>
          <div class="rail-stat"><strong data-count="355100" data-suffix=" zł">355 100 zł</strong><span>przychodu przypisanego kampaniom</span></div><i class="rail-sep"></i>
          <div class="platform-marquee">
            <div class="platform-track">
              <div class="platform-group">
                <span class="platform-item"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt=""> reklamy w Meta</span>
                <span class="platform-item"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt=""> intencja w Google</span>
                <span class="platform-item"><svg viewBox="0 0 24 24"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg> analityka</span>
                <span class="platform-item"><svg viewBox="0 0 24 24"><path d="M3 3v18h18M7 15l4-4 3 3 5-7"/></svg> optymalizacja</span>
                <span class="platform-item"><svg viewBox="0 0 24 24"><path d="M12 3v18M3 12h18"/></svg> kreacja</span>
              </div>
              <div class="platform-group">
                <span class="platform-item"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt=""> reklamy w Meta</span>
                <span class="platform-item"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt=""> intencja w Google</span>
                <span class="platform-item"><svg viewBox="0 0 24 24"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg> analityka</span>
                <span class="platform-item"><svg viewBox="0 0 24 24"><path d="M3 3v18h18M7 15l4-4 3 3 5-7"/></svg> optymalizacja</span>
                <span class="platform-item"><svg viewBox="0 0 24 24"><path d="M12 3v18M3 12h18"/></svg> kreacja</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section manifesto">
      <div class="wrap">
        <div class="manifesto-grid">
          <h2 class="manifesto-title reveal">Nie kupujemy ruchu. <strong>Projektujemy drogę</strong> do <em>decyzji.</em></h2>
          <div class="manifesto-side reveal" data-delay="1">
            <figure class="manifesto-person" aria-hidden="true"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>stanislaw-pointing.webp" width="1201" height="1860" alt="" loading="lazy" decoding="async"></figure>
            <p>Skuteczna kampania zaczyna się wcześniej niż w Menedżerze reklam. Trzeba zrozumieć <strong>ofertę, klienta, moment zakupu i miejsce, do którego kierujemy ruch.</strong> Dopiero wtedy dobieramy kanał, komunikat i budżet.</p>
          </div>
        </div>
        <div class="logic-rail logic-rail--cards reveal">
          <article class="logic-item" data-step="01"><span>01 / uwaga</span><i class="logic-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><path d="M12 3v2m0 14v2M3 12h2m14 0h2"/></svg></i><strong>Zatrzymujemy właściwą osobę.</strong><p>Kreacja i pierwsza sekunda zatrzymują właściwą grupę odbiorców — nie przypadkowy ruch.</p></article>
          <article class="logic-item" data-step="02"><span>02 / znaczenie</span><i class="logic-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h5"/></svg></i><strong>Pokazujemy powód, by zostać.</strong><p>Komunikat i oferta od razu tłumaczą, dlaczego warto poznać markę i zostać dłużej.</p></article>
          <article class="logic-item" data-step="03"><span>03 / zaufanie</span><i class="logic-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m12 3 7 3v5c0 4.6-2.8 8-7 10-4.2-2-7-5.4-7-10V6z"/><path d="m9 12 2 2 4-4"/></svg></i><strong>Usuwamy pytania i opór.</strong><p>Dowody, opinie i konkret rozwiewają wątpliwości przed kliknięciem i zapytaniem.</p></article>
          <article class="logic-item logic-item--accent" data-step="04"><span>04 / działanie</span><i class="logic-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg></i><strong>Prowadzimy do kolejnego kroku.</strong><p>Landing, formularz i pomiar prowadzą użytkownika prosto do telefonu lub zapytania.</p></article>
        </div>
      </div>
    </section>

    <section class="section packages" id="pakiety">
      <div class="ads-logo-backdrop" aria-hidden="true">
        <img class="ads-logo-bg ads-logo-bg--meta" src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="" loading="lazy" decoding="async">
        <img class="ads-logo-bg ads-logo-bg--google" src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="" loading="lazy" decoding="async">
      </div>
      <div class="wrap">
        <div class="section-head">
          <div class="section-head__copy reveal"><p class="section-kicker"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="M3.3 7 12 12l8.7-5"/><path d="M12 22V12"/></svg>Pakiety kampanii reklamowych</p><h2 class="section-title">Wybierz poziom obsługi dopasowany do <strong>etapu i tempa wzrostu.</strong></h2><p class="section-lead">Możemy zacząć od jednego celu i kanału, prowadzić regularne działania performance albo połączyć kampanie, kreacje, analitykę i stronę w pełny proces skalowania.</p></div>
          <p class="section-head__aside reveal" data-delay="1">Każdy przycisk otwiera Studio Wyceny z wybraną usługą i pakietem. Tam doprecyzujesz budżet, kanały, materiały i zakres współpracy.</p>
        </div>

        <div class="packages-grid">
          <article class="package-card reveal">
            <div class="package-person" aria-hidden="true"><img src="https://zaprojektowani.com/wp-content/uploads/2026/09/marta_pointing.webp" alt="" loading="lazy" decoding="async"></div>
            <div class="package-top"><span class="package-number">01 / start</span><span class="package-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v18M3 12h18"/><circle cx="12" cy="12" r="8"/></svg></span></div>
            <span class="package-tag">Dobry start</span>
            <h3>Starter Ads</h3>
            <p class="package-subtitle">Jeden główny cel • Meta Ads lub Google Ads</p>
            <p>Dla firm, które chcą poprawnie uruchomić pierwszą kampanię albo uporządkować podstawowe działania na jednym kanale.</p>
            <div class="package-best"><span>Najlepszy, gdy…</span><strong>Chcesz sprawdzić potencjał kampanii bez dokładania niepotrzebnych kanałów na starcie.</strong></div>
            <ul class="package-list">
              <li>analiza celu, oferty i strony docelowej,</li>
              <li>wybór Meta Ads albo Google Ads,</li>
              <li>konfiguracja lub uporządkowanie konta,</li>
              <li>pomiar najważniejszej konwersji,</li>
              <li>struktura kampanii i materiały startowe,</li>
              <li>optymalizacja oraz czytelne wnioski.</li>
            </ul>
            <a class="package-cta" href="https://zaprojektowani.com/studio-wyceny/?zpbs_service=ads&amp;zpbs_package=Starter%20Ads&amp;zpbs_source=kampanie-reklamowe-katowice#zpbsUltimate">Wybierz Starter Ads <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
          </article>

          <article class="package-card package-card--featured reveal" data-delay="1">
            <div class="package-person" aria-hidden="true"><img src="https://zaprojektowani.com/wp-content/uploads/2026/09/stanislaw_pointing.webp" alt="" loading="lazy" decoding="async"></div>
            <span class="package-ribbon">Najczęstszy wybór</span>
            <div class="package-top"><span class="package-number">02 / performance</span><span class="package-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3v18h18M7 15l4-4 3 3 5-7"/></svg></span></div>
            <span class="package-tag">Stała optymalizacja</span>
            <h3>Standard Performance</h3>
            <p class="package-subtitle">Stała obsługa • testy • remarketing • UTM</p>
            <p>Dla firm, które chcą regularnie pozyskiwać zapytania lub sprzedaż i potrzebują ciągłej pracy nad wynikiem.</p>
            <div class="package-best"><span>Najlepszy, gdy…</span><strong>Masz sprawdzoną ofertę i chcesz zbudować przewidywalny rytm pozyskiwania klientów.</strong></div>
            <ul class="package-list">
              <li>Meta Ads i/lub Google Ads dobrane do ścieżki,</li>
              <li>stałe prowadzenie i optymalizacja budżetu,</li>
              <li>testy kreacji, nagłówków i odbiorców,</li>
              <li>remarketing i kontrola jakości ruchu,</li>
              <li>konwersje, UTM i spójne raportowanie,</li>
              <li>rekomendacje dla oferty i landing page.</li>
            </ul>
            <a class="package-cta" href="https://zaprojektowani.com/studio-wyceny/?zpbs_service=ads&amp;zpbs_package=Standard%20Performance&amp;zpbs_source=kampanie-reklamowe-katowice#zpbsUltimate">Wybierz Standard Performance <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
          </article>

          <article class="package-card reveal" data-delay="2">
            <div class="package-person" aria-hidden="true"><img src="https://zaprojektowani.com/wp-content/uploads/2026/09/mateusz_pointing.webp" alt="" loading="lazy" decoding="async"></div>
            <div class="package-top"><span class="package-number">03 / growth</span><span class="package-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 16 4-4 4 3 7-8M19 7v5m0-5h-5"/></svg></span></div>
            <span class="package-tag">Pełny system wzrostu</span>
            <h3>Premium Growth Ads</h3>
            <p class="package-subtitle">Meta + Google + kreacje + analityka + skalowanie</p>
            <p>Dla marek, które chcą rozwijać kampanie szybciej i potrzebują partnera łączącego media, kreację, technologię oraz stronę.</p>
            <div class="package-best"><span>Najlepszy, gdy…</span><strong>Kampanie są ważnym kanałem sprzedaży, a wynik zależy od szybkich testów i współpracy specjalistów o różnych kompetencjach.</strong></div>
            <ul class="package-list">
              <li>spójna strategia Meta Ads i Google Ads,</li>
              <li>Pixel, CAPI i rozbudowany pomiar,</li>
              <li>regularnie przygotowywane kreacje i warianty tekstów reklamowych,</li>
              <li>prospecting, remarketing i kampanie intencyjne,</li>
              <li>katalog produktowy lub e-commerce,</li>
              <li>plan testów i skalowania oraz cykliczne przeglądy strategii.</li>
            </ul>
            <a class="package-cta" href="https://zaprojektowani.com/studio-wyceny/?zpbs_service=ads&amp;zpbs_package=Premium%20Growth%20Ads&amp;zpbs_source=kampanie-reklamowe-katowice#zpbsUltimate">Wybierz Premium Growth Ads <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
          </article>
        </div>

        <div class="packages-bottom reveal">
          <div><strong>Nie wiesz, który zakres będzie właściwy?</strong><p>W Studio Wyceny podasz cel, budżet mediowy, branżę, kanały i dostępne materiały. Na tej podstawie dobierzemy poziom obsługi i wskażemy najlepszy punkt startu.</p></div>
          <a class="btn" href="https://zaprojektowani.com/studio-wyceny/?zpbs_service=ads#zpbsUltimate">Przejdź do Studia Wyceny <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
        </div>
      </div>
    </section>


    <section class="section channels" id="kanaly">
      <div class="wrap">
        <div class="section-head">
          <div class="section-head__copy reveal">
            <p class="section-kicker section-kicker--light"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/></svg>Dwa kanały • jeden system</p>
            <h2 class="section-title">Google łapie intencję. Meta <strong class="gradient-text">buduje popyt.</strong></h2>
          </div>
          <p class="section-head__aside reveal" data-delay="1">Nie ustawiamy platform przeciwko sobie. Każda odpowiada za inny moment ścieżki — razem tworzą pełniejszy obraz klienta.</p>
        </div>

        <div class="channel-grid">
          <article class="channel-card meta-card reveal">
            <span class="channel-card__index">01</span>
            <span class="channel-clip" aria-hidden="true"><img class="channel-watermark" src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="" loading="lazy" decoding="async"></span>
            <div class="channel-card__copy">
              <div class="channel-logo"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="Meta Ads"></div>
              <h3>Docieramy, zanim klient <strong>zacznie szukać.</strong></h3>
              <p>Meta Ads pozwala budować zainteresowanie obrazem, historią i dobrze dobraną obietnicą. Tworzymy kampanie na Facebooku i Instagramie, które wspierają <strong>rozpoznawalność, ruch, leady, rezerwacje i sprzedaż.</strong></p>
              <div class="channel-list"><span>Facebook</span><span>Instagram</span><span>Reels</span><span>Stories</span><span>remarketing</span></div>
            </div>
            <div class="channel-mockup" aria-label="Reklama Apartamentu Piękna w feedzie Facebooka">
              <div class="fb-ad">
                <div class="fb-ad__head">
                  <span class="brand-avatar brand-avatar--ap"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-logo.webp" alt=""></span>
                  <div class="fb-ad__id"><strong>Apartament Piękna</strong><span>Sponsorowane · <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3.6 9h16.8M3.6 15h16.8M12 3a15.5 15.5 0 0 1 0 18 15.5 15.5 0 0 1 0-18z"/></svg></span></div>
                  <span class="fb-ad__menu" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.7" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.7" fill="currentColor" stroke="none"/><circle cx="19" cy="12" r="1.7" fill="currentColor" stroke="none"/></svg><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></span>
                </div>
                <div class="fb-ad__copy">Naturalny efekt zaczyna się od dobrej konsultacji. Poznaj plan zabiegowy dopasowany do Twojej skóry ✨</div>
                <div class="fb-ad__media"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-photo-wolumetria.webp" alt="Zabieg w Apartamencie Piękna" loading="lazy" decoding="async"></div>
                <div class="fb-ad__link"><div><span>apartamentpiekna.pl</span><strong>Indywidualna konsultacja w sercu Katowic</strong></div><b class="fb-ad__btn">Zarezerwuj</b></div>
                <div class="fb-ad__social"><span class="fb-reactions"><em><i class="like"><svg viewBox="0 0 24 24"><path d="M2 21h4V9H2v12zM23 10c0-1.1-.9-2-2-2h-6.3l1-4.6v-.3c0-.4-.2-.8-.4-1.1L14.2 1 7.6 7.6c-.4.3-.6.8-.6 1.4v10c0 1.1.9 2 2 2h9c.8 0 1.5-.5 1.8-1.2l3-7.1c.1-.2.2-.5.2-.7v-2z"/></svg></i><i class="love"><svg viewBox="0 0 24 24"><path d="M12 21s-8-5.3-8-11a4.6 4.6 0 0 1 8-3.1A4.6 4.6 0 0 1 20 10c0 5.7-8 11-8 11z"/></svg></i></em> 214</span><span>18 komentarzy · 9 udostępnień</span></div>
                <div class="fb-ad__actions">
                  <span><svg viewBox="0 0 24 24"><path d="M7 10v11m0-11 4.2-6.4c.5-.8 1.6-.9 2.3-.3.5.4.7 1 .6 1.6L13.4 10H19a2 2 0 0 1 2 2.4l-1.4 6.8A2.4 2.4 0 0 1 17.2 21H7m0-11H4a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h3"/></svg> Lubię to!</span>
                  <span><svg viewBox="0 0 24 24"><path d="M21 12a8 8 0 0 1-8 8H4l2.4-2.9A8 8 0 1 1 21 12z"/></svg> Skomentuj</span>
                  <span><svg viewBox="0 0 24 24"><path d="m14 5 7 7-7 7v-4.2C7 14.8 4 17.5 3 20c0-6 3.5-10.4 11-10.8V5z"/></svg> Udostępnij</span>
                </div>
              </div>
            </div>
          </article>

          <article class="channel-card google-card reveal" data-delay="1">
            <span class="channel-card__index">02</span>
            <span class="channel-clip" aria-hidden="true"><img class="channel-watermark" src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="" loading="lazy" decoding="async"></span>
            <div class="channel-card__copy">
              <div class="channel-logo"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="Google Ads"></div>
              <h3>Jesteśmy tam, gdzie klient <strong>już pyta.</strong></h3>
              <p>Google Ads przechwytuje realną intencję: usługę, produkt, problem albo lokalizację. Porządkujemy strukturę kampanii, słowa kluczowe, komunikaty i strony docelowe, by <strong>nie przepalać budżetu na przypadkowe kliknięcia.</strong></p>
              <div class="channel-list"><span>Search</span><span>Performance Max</span><span>remarketing</span><span>lokalnie</span><span>e-commerce</span></div>
            </div>
            <div class="channel-mockup" aria-label="Reklama Krawca z Dojazdem w wynikach wyszukiwania Google">
              <div class="mini-serp">
                <div class="mini-serp__top">
                  <svg class="g-logo" viewBox="0 0 48 48" aria-hidden="true"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                  <div class="serp-pill"><span>garnitur szyty na miarę</span><svg class="serp-x" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg><i class="serp-sep"></i><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"/><path fill="#34A853" d="M17 11a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.92V21h2v-3.08A7 7 0 0 0 19 11h-2z"/><path fill="#FBBC05" d="M5 11h2a5 5 0 0 0 .35 1.83l-1.6 1.25A7 7 0 0 1 5 11z"/><path fill="#EA4335" d="M12 3a3 3 0 0 0-3 3v.3l5.9-.02A3 3 0 0 0 12 3z" opacity="0"/></svg><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#EA4335" d="M6 3h5v2H6a1 1 0 0 0-1 1v5H3V6a3 3 0 0 1 3-3z"/><path fill="#4285F4" d="M18 3a3 3 0 0 1 3 3v5h-2V6a1 1 0 0 0-1-1h-5V3h5z"/><path fill="#34A853" d="M5 13v5a1 1 0 0 0 1 1h5v2H6a3 3 0 0 1-3-3v-5h2z"/><path fill="#FBBC05" d="M21 13v5a3 3 0 0 1-3 3h-5v-2h5a1 1 0 0 0 1-1v-5h2z"/><circle cx="12" cy="12" r="3.2" fill="#4285F4"/></svg></div>
                </div>
                <div class="serp-ad">
                  <span class="serp-sponsored">Sponsorowane</span>
                  <div class="serp-src"><i class="serp-favicon"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>krawiec-logo.webp" alt=""></i><div><strong>Krawiec z Dojazdem</strong><span>https://krawieczdojazdem.pl</span></div></div>
                  <span class="serp-title">Garnitur na miarę — spotkanie tam, gdzie Ci wygodnie</span>
                  <p class="serp-desc">Tkaniny premium, pełne doradztwo i <b>dojazd w całej Polsce</b>. Umów bezpłatną konsultację — odpowiadamy tego samego dnia.</p>
                  <div class="serp-sitelinks"><span>Umów przymiarkę</span><span>Tkaniny i wzory</span><span>Proces szycia</span><span>Opinie klientów</span></div>
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section class="section ad-showcase" id="formaty">
      <div class="wrap">
        <div class="section-head">
          <div class="section-head__copy reveal">
            <p class="section-kicker"><svg viewBox="0 0 24 24" aria-hidden="true"><rect width="7" height="7" x="3" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="14" rx="1.5"/><rect width="7" height="7" x="3" y="14" rx="1.5"/></svg>Gdzie pracuje Twoja reklama</p>
            <h2 class="section-title">Jedna idea. <strong>Format dopasowany do miejsca i momentu.</strong></h2>
            <p class="section-lead">Reklama w feedzie ma zatrzymać kciuk, Stories ma przekazać komunikat w kilka sekund, a wynik w Google — dać najtrafniejszą odpowiedź. Projektujemy każdy format z myślą o jego konkretnej roli.</p>
          </div>
          <p class="section-head__aside reveal" data-delay="1">Kreacja, tekst i CTA nie są kopiowane mechanicznie. Zmieniamy hierarchię i tempo komunikatu tak, by reklama była czytelna dokładnie tam, gdzie zobaczy ją klient.</p>
        </div>

        <div class="placement-grid">
          <article class="placement-card placement-card--feed reveal">
            <div class="placement-top"><span>Facebook / Instagram Feed</span><span class="placement-logo"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="Meta Ads"></span></div>
            <h3 class="placement-title">Kreacja, która zatrzymuje i od razu prowadzi do oferty.<small>Format do budowania zainteresowania, remarketingu i pozyskiwania zapytań.</small></h3>
            <div class="fb-ad fb-ad--lg" aria-label="Reklama Apartamentu Piękna w feedzie Facebooka">
              <div class="fb-ad__head">
                <span class="brand-avatar brand-avatar--ap"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-logo.webp" alt=""></span>
                <div class="fb-ad__id"><strong>Apartament Piękna</strong><span>Sponsorowane · <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3.6 9h16.8M3.6 15h16.8M12 3a15.5 15.5 0 0 1 0 18 15.5 15.5 0 0 1 0-18z"/></svg></span></div>
                <span class="fb-ad__menu" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.7" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.7" fill="currentColor" stroke="none"/><circle cx="19" cy="12" r="1.7" fill="currentColor" stroke="none"/></svg><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></span>
              </div>
              <div class="fb-ad__copy">Naturalny efekt zaczyna się od dobrej konsultacji. Poznaj zabiegi dopasowane do potrzeb Twojej skóry 💧</div>
              <div class="fb-ad__media"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-photo-unnamed.jpg" alt="Zespół Apartamentu Piękna" loading="lazy" decoding="async"></div>
              <div class="fb-ad__link"><div><span>apartamentpiekna.pl</span><strong>Umów wizytę w Apartamencie Piękna</strong></div><b class="fb-ad__btn">Umów wizytę</b></div>
              <div class="fb-ad__social"><span class="fb-reactions"><em><i class="like"><svg viewBox="0 0 24 24"><path d="M2 21h4V9H2v12zM23 10c0-1.1-.9-2-2-2h-6.3l1-4.6v-.3c0-.4-.2-.8-.4-1.1L14.2 1 7.6 7.6c-.4.3-.6.8-.6 1.4v10c0 1.1.9 2 2 2h9c.8 0 1.5-.5 1.8-1.2l3-7.1c.1-.2.2-.5.2-.7v-2z"/></svg></i><i class="love"><svg viewBox="0 0 24 24"><path d="M12 21s-8-5.3-8-11a4.6 4.6 0 0 1 8-3.1A4.6 4.6 0 0 1 20 10c0 5.7-8 11-8 11z"/></svg></i></em> 356</span><span>27 komentarzy · 14 udostępnień</span></div>
              <div class="fb-ad__actions">
                <span><svg viewBox="0 0 24 24"><path d="M7 10v11m0-11 4.2-6.4c.5-.8 1.6-.9 2.3-.3.5.4.7 1 .6 1.6L13.4 10H19a2 2 0 0 1 2 2.4l-1.4 6.8A2.4 2.4 0 0 1 17.2 21H7m0-11H4a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h3"/></svg> Lubię to!</span>
                <span><svg viewBox="0 0 24 24"><path d="M21 12a8 8 0 0 1-8 8H4l2.4-2.9A8 8 0 1 1 21 12z"/></svg> Skomentuj</span>
                <span><svg viewBox="0 0 24 24"><path d="m14 5 7 7-7 7v-4.2C7 14.8 4 17.5 3 20c0-6 3.5-10.4 11-10.8V5z"/></svg> Udostępnij</span>
              </div>
            </div>
          </article>

          <article class="placement-card placement-card--story reveal" data-delay="1">
            <div class="placement-top"><span>Stories / Reels</span><span class="placement-logo"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="Meta Ads"></span></div>
            <h3 class="placement-title">Pełny ekran. Jedna myśl. Jasny kolejny krok.<small>Dynamiczny format do pokazania efektu, procesu albo mocnej przewagi marki.</small></h3>
            <div class="story-phone" aria-label="Reklama Apartamentu Piękna w Instagram Stories">
              <img class="story-photo" src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-photo-att.jpg" alt="Efekt zabiegu ust w Apartamencie Piękna" loading="lazy" decoding="async">
              <div class="story-shade" aria-hidden="true"></div>
              <div class="story-top">
                <div class="story-progress" aria-hidden="true"><i></i><i></i><i></i></div>
                <div class="story-head">
                  <span class="ig-avatar"><i><img src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-logo.webp" alt=""></i></span>
                  <div><strong>apartament.piekna</strong><small>Sponsorowane</small></div>
                  <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.4" fill="#fff" stroke="none"/><circle cx="12" cy="12" r="1.4" fill="#fff" stroke="none"/><circle cx="19" cy="12" r="1.4" fill="#fff" stroke="none"/></svg>
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </div>
              </div>
              <div class="story-caption"><strong>Twoja skóra. Twój plan.</strong><span>Konsultacja w centrum Katowic</span></div>
              <div class="story-cta"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 14 6-6 6 6"/></svg><b>Umów wizytę</b></div>
            </div>
          </article>

          <article class="placement-card placement-card--search reveal">
            <div class="placement-top"><span>Google Search</span><span class="placement-logo"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="Google Ads"></span></div>
            <h3 class="placement-title">Odpowiedź dokładnie wtedy, gdy pojawia się potrzeba.<small>Komunikat oparty na intencji, lokalizacji i konkretnym następnym kroku.</small></h3>
            <div class="search-browser" aria-label="Reklama Krawca z Dojazdem w wynikach Google">
              <div class="browser-top"><i></i><i></i><i></i><span>google.com/search?q=krawiec+z+dojazdem+garnitur+na+miarę</span></div>
              <div class="serp-head">
                <svg class="g-logo" viewBox="0 0 48 48" aria-hidden="true"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                <div class="serp-pill"><span>krawiec z dojazdem garnitur na miarę</span><svg class="serp-x" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg><i class="serp-sep"></i><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"/><path fill="#34A853" d="M17 11a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.92V21h2v-3.08A7 7 0 0 0 19 11h-2z"/></svg><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#EA4335" d="M6 3h5v2H6a1 1 0 0 0-1 1v5H3V6a3 3 0 0 1 3-3z"/><path fill="#4285F4" d="M18 3a3 3 0 0 1 3 3v5h-2V6a1 1 0 0 0-1-1h-5V3h5z"/><path fill="#34A853" d="M5 13v5a1 1 0 0 0 1 1h5v2H6a3 3 0 0 1-3-3v-5h2z"/><path fill="#FBBC05" d="M21 13v5a3 3 0 0 1-3 3h-5v-2h5a1 1 0 0 0 1-1v-5h2z"/><circle cx="12" cy="12" r="3.2" fill="#4285F4"/></svg></div>
              </div>
              <div class="serp-tabs"><b>Wszystkie</b><span>Grafika</span><span>Mapy</span><span>Wideo</span><span>Zakupy</span><span>Więcej</span></div>
              <div class="serp-ad serp-ad--lg">
                <span class="serp-sponsored">Sponsorowane</span>
                <div class="serp-src"><i class="serp-favicon"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>krawiec-logo.webp" alt=""></i><div><strong>Krawiec z Dojazdem</strong><span>https://krawieczdojazdem.pl</span></div></div>
                <span class="serp-title">Garnitur na miarę — spotkanie w miejscu, które wybierzesz</span>
                <p class="serp-desc">Poznaj tkaniny, proces i usługę premium z <b>dojazdem w całej Polsce</b>. Umów bezpłatną rozmowę i wybierz dogodny termin spotkania. Pełne doradztwo stylisty.</p>
                <div class="serp-sitelinks"><span>Umów przymiarkę</span><span>Tkaniny i wzory</span><span>Proces szycia krok po kroku</span><span>Opinie klientów</span></div>
              </div>
            </div>
          </article>

          <article class="placement-card placement-card--display reveal" data-delay="1">
            <div class="placement-top"><span>Display / Performance Max</span><span class="placement-logo"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="Google Ads"></span></div>
            <h3 class="placement-title">Marka wraca w odpowiednim kontekście.<small>Banery do budowania zasięgu i przypominania o ofercie osobom, które już ją poznały.</small></h3>
            <div class="display-canvas" aria-label="Baner remarketingowy Apartamentu Piękna"><div class="display-banner"><span class="ad-badge" aria-hidden="true"><i><svg viewBox="0 0 24 24"><path d="M5 3.5 20 12 5 20.5z"/></svg></i><i><svg class="x" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></i></span><img class="display-brand-logo" src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-logo.webp" alt="Apartament Piękna"><strong>Wróć do planu stworzonego dla Ciebie.</strong><b>Umów konsultację</b><img class="display-photo" src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-photo-att.jpg" alt="Efekt zabiegu w Apartamencie Piękna" loading="lazy" decoding="async"></div></div>
          </article>
        </div>
      </div>
    </section>

    <section class="pointing">
      <div class="wrap pointing-wrap">
        <div class="pointing-copy reveal">
          <p class="section-kicker"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>Reklama + strona docelowa</p>
          <h2>Dobre reklamy potrzebują <strong>dobrego miejsca do lądowania.</strong></h2>
          <p>Jeżeli oferta jest nieczytelna, formularz za długi, a wersja mobilna walczy z użytkownikiem — nawet najlepsza kampania ma związane ręce. Dlatego patrzymy na cały system: reklamę, landing page, kontakt i pomiar.</p>
          <a class="btn" href="#zakres">Zobacz pełny zakres <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
        </div>
        <div class="pointing-visual reveal" data-delay="1"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>stanislaw-pointing.webp" alt="Stanisław z zespołu Zaprojektowani wskazuje kolejny krok" loading="lazy" decoding="async"></div>
        <div class="pointing-note reveal" data-delay="2"><span>Wspólny mianownik</span><strong>Oferta → kreacja → landing → pomiar</strong></div>
      </div>
    </section>

    <section class="section process" id="proces">
      <div class="wrap">
        <div class="process-headline reveal">
          <p class="section-kicker"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><path d="M7 11v4a2 2 0 0 0 2 2h4"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>Proces kampanii</p>
          <h2 class="section-title">Od hipotezy do decyzji. <strong>Bez chaosu między kliknięciami.</strong></h2>
          <p class="section-lead">Każdy etap ma jasno określony cel, zakres odpowiedzialności i kryterium oceny. Dzięki temu wiemy, <strong>co testujemy, dlaczego to robimy i kiedy warto zmienić kierunek.</strong></p>
        </div>
        <figure class="process-person-stage reveal" data-delay="1" aria-hidden="true">
          <img src="<?php echo esc_url($zp_campaign_asset_url); ?>mateusz-sitting.webp" alt="" loading="lazy" decoding="async">
          <figcaption><span>6 etapów</span><strong>Jedna spójna ścieżka</strong></figcaption>
        </figure>
        <div class="process-track">
          <article class="process-step"><span class="process-num">01</span><div class="process-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2"/></svg></div><h3>Cel i ekonomia</h3><p>Ustalamy, czym jest wartościowa konwersja, jaki jest cykl decyzji i co naprawdę ma znaczenie dla biznesu.</p></article>
          <article class="process-step"><span class="process-num">02</span><div class="process-icon"><svg viewBox="0 0 24 24"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg></div><h3>Dane i pomiar</h3><p>Porządkujemy zdarzenia, analitykę, piksele, tagi i sposób raportowania najważniejszych działań.</p></article>
          <article class="process-step"><span class="process-num">03</span><div class="process-icon"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM8 9h8M8 13h5"/></svg></div><h3>Architektura</h3><p>Dobieramy kanały, grupy odbiorców, słowa kluczowe, strukturę kampanii i budżet testowy.</p></article>
          <article class="process-step"><span class="process-num">04</span><div class="process-icon"><svg viewBox="0 0 24 24"><path d="m4 16 4-4 4 3 7-8M19 7v5m0-5h-5"/></svg></div><h3>Kreacje i copy</h3><p>Tworzymy komunikaty, formaty i warianty reklam dopasowane do kanału oraz etapu decyzji.</p></article>
          <article class="process-step"><span class="process-num">05</span><div class="process-icon"><svg viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3m8 0h3a2 2 0 0 0 2-2v-3"/><circle cx="12" cy="12" r="3"/></svg></div><h3>Test i nauka</h3><p>Uruchamiamy kontrolowane testy, odcinamy przypadkowy ruch i wyciągamy wnioski z zachowania odbiorców.</p></article>
          <article class="process-step"><span class="process-num">06</span><div class="process-icon"><svg viewBox="0 0 24 24"><path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8M18.4 5.6 5.6 18.4"/></svg></div><h3>Optymalizacja</h3><p>Przesuwamy akcenty tam, gdzie dane pokazują potencjał — w kampanii, kreacji albo na stronie docelowej.</p></article>
        </div>
      </div>
    </section>

    <section class="section dashboard-section" id="analityka">
      <div class="ads-logo-backdrop" aria-hidden="true">
        <img class="ads-logo-bg ads-logo-bg--meta" src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="" loading="lazy" decoding="async">
        <img class="ads-logo-bg ads-logo-bg--google" src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="" loading="lazy" decoding="async">
      </div>
      <div class="wrap">
        <div class="section-head">
          <div class="section-head__copy reveal"><p class="section-kicker section-kicker--light"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>Analityka bez teatru liczb</p><h2 class="section-title">Nie raportujemy kliknięć dla raportu. <strong class="gradient-text">Pokazujemy wynik dla biznesu.</strong></h2><p class="section-lead" style="color:rgba(255,255,255,.58)">Tak wygląda kokpit kampanii Krawca z Dojazdem: budżet, zapytania, potwierdzone zlecenia i przychód przypisany kampaniom w jednym widoku. Widać, który kanał przechwytuje popyt i gdzie warto przesunąć kolejny budżet.</p></div>
          <div class="reveal" data-delay="1"><span class="data-note"><i></i> wyniki klienta: Krawiec z Dojazdem • 90 dni</span></div>
        </div>

        <div class="campaign-cockpit reveal" aria-label="Pulpit analityczny kampanii Krawca z Dojazdem — Meta Ads i Google Ads">
          <div class="cockpit-top">
            <div class="cockpit-brand"><span class="cockpit-mark"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>krawiec-logo.webp" alt="Krawiec z Dojazdem"></span><div><strong>Krawiec z Dojazdem</strong><span>wyniki kampanii • Meta + Google + sprzedaż</span></div></div>
            <span class="cockpit-status"><i></i> synchronizacja: dziś 09:42</span>
          </div>
          <div class="cockpit-layout">
            <div class="cockpit-main">
              <div class="metric-row">
                <div class="metric-card"><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>Budżet mediowy</span><strong data-count="27500" data-suffix=" zł">27 500 zł</strong><small>Meta + Google, bez kosztu obsługi</small></div>
                <div class="metric-card"><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 7 13.5 15.5 8.5 10.5 2 17"/><path d="M16 7h6v6"/></svg>Przychód przypisany</span><strong data-count="215400" data-suffix=" zł">215 400 zł</strong><small class="up">▲ +41% vs poprzednie 90 dni</small></div>
                <div class="metric-card"><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>ROAS</span><strong data-count="7.83" data-decimals="2" data-suffix="×">7,83×</strong><small>7,83 zł przychodu z 1 zł budżetu</small></div>
                <div class="metric-card"><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>Kwalifikowane zapytania</span><strong data-count="158">158</strong><small class="up">174 zł za kontakt • ▲ +34%</small></div>
              </div>
              <div class="chart-card">
                <div class="card-heading"><strong><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M7 15l4-5 4 3 5-7"/></svg>Przychód przypisany tydzień po tygodniu</strong><span class="chart-delta">▲ +41% kw/kw</span></div>
                <div class="legend"><span><i></i> Google Ads — aktywna intencja</span><span><i></i> Meta Ads — zainteresowanie i powrót</span></div>
                <svg class="big-chart" viewBox="0 0 760 300" role="img" aria-label="Przychód przypisany kampaniom tydzień po tygodniu — Google Ads i Meta Ads">
                  <path class="grid" d="M45 20v235M45 255h690M45 200h690M45 145h690M45 90h690M45 35h690"/>
                  <text class="axis" x="8" y="258">0</text>
                  <text class="axis" x="8" y="203">6k</text>
                  <text class="axis" x="8" y="148">12k</text>
                  <text class="axis" x="8" y="93">18k</text>
                  <text class="axis" x="8" y="38">24k</text>
                  <text class="axis" x="44" y="282">13 TYGODNI TEMU</text><text class="axis" x="716" y="282">DZIŚ</text>
                  <path class="line-a" d="M45 232 C 95 226, 125 214, 175 207 S 265 196, 315 178 S 400 165, 450 142 S 545 116, 605 96 S 700 74, 735 60"/>
                  <path class="line-b" d="M45 245 C 100 241, 140 233, 190 227 S 285 214, 335 205 S 425 194, 475 177 S 575 158, 635 140 S 705 122, 735 112"/>
                  <circle class="dot" cx="735" cy="60" r="5" fill="#8ec8f7"/>
                  <circle class="dot-ring" cx="735" cy="60" r="6" fill="none" stroke="#8ec8f7" stroke-width="1.5"/>
                  <circle class="dot" cx="735" cy="112" r="5" fill="#7c82ff"/>
                  <circle class="dot-ring" cx="735" cy="112" r="6" fill="none" stroke="#7c82ff" stroke-width="1.5"/>
                </svg>
              </div>
            </div>
            <aside class="cockpit-side">
              <div class="funnel-card">
                <div class="card-heading"><strong><svg viewBox="0 0 24 24" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>Od kliknięcia do przychodu</strong><span>90 dni</span></div>
                <div class="funnel-bars">
                  <div class="funnel-bar"><span>Kliknięcia</span><strong data-count="18420">18 420</strong><i style="--w:100%"></i></div>
                  <div class="funnel-bar"><span>Sesje zaangażowane</span><strong data-count="12380">12 380</strong><i style="--w:67%"></i></div>
                  <div class="funnel-bar"><span>Kwalifikowane zapytania</span><strong data-count="158">158</strong><i style="--w:46%"></i></div>
                  <div class="funnel-bar"><span>Potwierdzone zlecenia</span><strong data-count="63">63</strong><i style="--w:30%"></i></div>
                </div>
              </div>
              <div class="mix-card">
                <div class="card-heading"><strong><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>Udział w przychodzie</strong><span>kanały</span></div>
                <div class="mix-content"><div class="donut" id="mixDonut" data-a="72.4" style="background:conic-gradient(#8ec8f7 0 0%,#7c82ff 0% 100%)"><strong data-count="7.83" data-decimals="2" data-suffix="×">7,83×</strong><small>ROAS</small></div><div class="mix-legend"><span><i></i> Google Ads <b>72,4%</b></span><span><i></i> Meta Ads <b>27,6%</b></span></div></div>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </section>

    <section class="section cases" id="realizacje">
      <div class="wrap">
        <div class="section-head">
          <div class="section-head__copy reveal"><p class="section-kicker"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>Przykłady współpracy</p><h2 class="section-title">Dwa biznesy. Dwie ścieżki zakupu. <strong>Jeden sposób myślenia.</strong></h2></div>
          <p class="section-head__aside reveal" data-delay="1">Wynik powstaje na styku kanału, komunikatu, strony i obsługi zapytania. Dlatego pokazujemy nie tylko zakres działań, ale też liczby, które powinny prowadzić do kolejnej decyzji.</p>
        </div>

        <div class="case-stack">
          <article class="case-card case-card--beauty reveal">
            <div class="case-card__inner">
              <div class="case-copy">
                <span class="case-index"><i></i> 01 / beauty & medycyna estetyczna</span>
                <img class="case-client-logo case-client-logo--ap" src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-logo.webp" alt="Apartament Piękna" loading="lazy" decoding="async">
                <div class="case-platforms"><span><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="Meta Ads"></span><span><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="Google Ads"></span><em>dwa kanały • jedna ścieżka do rezerwacji</em></div>
                <h3>Apartament <strong>Piękna</strong></h3>
                <p>W branży beauty decyzja zaczyna się od emocji i zaufania, ale domyka ją konkret: właściwy zabieg, lokalizacja, specjalistka i prosty sposób rezerwacji. Meta Ads buduje zainteresowanie ofertą i wraca do osób, które poznały markę. Google Ads odpowiada wtedy, gdy klientka aktywnie szuka zabiegu w Katowicach.</p>
                <div class="case-tags"><span>Google Search</span><span>Meta Ads</span><span>remarketing</span><span>lokalny popyt</span></div>
                <div class="case-results-title"><span>Meta + Google • ostatnie 90 dni</span></div>
                <div class="case-results">
                  <div class="case-result case-result--hero"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M22 7 13.5 15.5 8.5 10.5 2 17"/><path d="M16 7h6v6"/></svg></i><span>Przychód przypisany kampaniom</span><strong data-count="139700" data-suffix=" zł">139 700 zł</strong><small>budżet mediowy: 21 600 zł</small></div>
                  <div class="case-result"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg></i><span>ROAS</span><strong data-count="6.47" data-decimals="2" data-suffix="×">6,47×</strong><small>6,47 zł przychodu z 1 zł budżetu</small></div>
                  <div class="case-result"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg></i><span>Wartościowe zapytania</span><strong data-count="346">346</strong><small>62 zł za zapytanie</small></div>
                  <div class="case-result"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18M9 16l2 2 4-4"/></svg></i><span>Opłacone wizyty</span><strong data-count="181">181</strong><small>119 zł za opłaconą wizytę</small></div>
                </div>
              </div>
              <div class="case-visual"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>apartament-mockup.webp" alt="Apartament Piękna — strona internetowa na laptopie i telefonie" loading="lazy" decoding="async"><div class="case-badge"><span>Punkt styku</span><strong>Reklama spotyka ofertę przygotowaną pod decyzję.</strong></div></div>
            </div>
          </article>

          <article class="case-card case-card--tailor reveal">
            <div class="case-card__inner">
              <div class="case-visual"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>kaminski-mockup.webp" alt="Krawiec z Dojazdem — strona internetowa na laptopie i telefonie" loading="lazy" decoding="async"><div class="case-badge"><span>Punkt styku</span><strong>Wysoka intencja wymaga konkretnej odpowiedzi.</strong></div></div>
              <div class="case-copy">
                <span class="case-index"><i></i> 02 / usługa premium z dojazdem</span>
                <img class="case-client-logo case-client-logo--krawiec" src="<?php echo esc_url($zp_campaign_asset_url); ?>krawiec-logo.webp" alt="Krawiec z Dojazdem" loading="lazy" decoding="async">
                <div class="case-platforms"><span><img src="<?php echo esc_url($zp_campaign_asset_url); ?>google-ads-logo.png" alt="Google Ads"></span><span><img src="<?php echo esc_url($zp_campaign_asset_url); ?>meta-ads-logo.png" alt="Meta Ads"></span><em>wysoka intencja • marka premium</em></div>
                <h3>Krawiec <strong>z Dojazdem</strong></h3>
                <p>Przy szyciu na miarę klient kupuje proces, wygodę i pewność wyboru. Google Ads pojawia się wtedy, gdy ktoś szuka garnituru na miarę lub krawca premium. Meta Ads pokazuje tkaniny, detale i charakter spotkania — wszystko, czego nie da się zamknąć w samym haśle.</p>
                <div class="case-tags"><span>Google Ads</span><span>Meta Ads</span><span>lead generation</span><span>cała Polska</span></div>
                <div class="case-results-title"><span>Google + Meta • ostatnie 90 dni</span></div>
                <div class="case-results">
                  <div class="case-result case-result--hero"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M22 7 13.5 15.5 8.5 10.5 2 17"/><path d="M16 7h6v6"/></svg></i><span>Przychód przypisany kampaniom</span><strong data-count="215400" data-suffix=" zł">215 400 zł</strong><small>budżet mediowy: 27 500 zł</small></div>
                  <div class="case-result"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg></i><span>ROAS</span><strong data-count="7.83" data-decimals="2" data-suffix="×">7,83×</strong><small>7,83 zł przychodu z 1 zł budżetu</small></div>
                  <div class="case-result"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg></i><span>Kwalifikowane zapytania</span><strong data-count="158">158</strong><small>174 zł za kwalifikowany kontakt</small></div>
                  <div class="case-result"><i class="case-result__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg></i><span>Potwierdzone zlecenia</span><strong data-count="63">63</strong><small>średnia wartość zlecenia: 3 419 zł</small></div>
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section class="section scope" id="zakres">
      <div class="wrap">
        <div class="section-head">
          <div class="section-head__copy reveal"><p class="section-kicker"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>Co bierzemy na siebie</p><h2 class="section-title">Nie tylko ustawienia. <strong>Cały system kampanii.</strong></h2></div>
          <p class="section-head__aside reveal" data-delay="1">Zakres dobieramy do etapu firmy i celu. Bez pakowania niepotrzebnych elementów do oferty.</p>
        </div>
        <div class="scope-grid">
          <article class="scope-card reveal"><span class="scope-num">01 / fundament</span><div class="scope-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2"/></svg></div><h3>Strategia i architektura kampanii</h3><p>Zaczynamy od celu biznesowego i ekonomii: co ma przynieść kampania, ile jest warty klient i po czym poznamy, że działa. Na tej podstawie dobieramy grupy odbiorców, intencje, ofertę, kanały i budżet testowy. Projektujemy podział kampanii, plan pierwszych eksperymentów oraz jasne zasady decyzji — kiedy skalujemy, a kiedy zmieniamy kierunek, zanim budżet zacznie pracować w ciemno.</p><img class="scope-watermark" src="<?php echo esc_url($zp_campaign_asset_url); ?>zp-sygnet-ciemny.webp" alt="" loading="lazy" decoding="async"></article>
          <article class="scope-card reveal" data-delay="1"><span class="scope-num">02 / kreacja</span><div class="scope-icon"><svg viewBox="0 0 24 24"><path d="M4 4h16v16H4zM8 15l3-3 2 2 3-4 3 5"/><circle cx="9" cy="9" r="1"/></svg></div><h3>Kreacje, formaty i teksty reklam</h3><p>Warianty komunikatów i grafik dopasowane do feedu, Stories, Reels, wyszukiwarki oraz etapu decyzji klienta.</p></article>
          <article class="scope-card reveal"><span class="scope-num">03 / setup</span><div class="scope-icon"><svg viewBox="0 0 24 24"><path d="M12 2v4m0 12v4M4.9 4.9l2.8 2.8m8.6 8.6 2.8 2.8M2 12h4m12 0h4M4.9 19.1l2.8-2.8m8.6-8.6 2.8-2.8"/><circle cx="12" cy="12" r="4"/></svg></div><h3>Konfiguracja kont</h3><p>Porządek w strukturze, dostępach i niezbędnych integracjach.</p></article>
          <article class="scope-card reveal" data-delay="1"><span class="scope-num">04 / pomiar</span><div class="scope-icon"><svg viewBox="0 0 24 24"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg></div><h3>Analityka i konwersje</h3><p>Zdarzenia, piksele, tagi i raportowanie oparte na działaniach, które mają znaczenie.</p></article>
          <article class="scope-card reveal"><span class="scope-num">05 / rozwój</span><div class="scope-icon"><svg viewBox="0 0 24 24"><path d="M3 3v18h18M7 15l4-4 3 3 5-7"/></svg></div><h3>Optymalizacja i testy</h3><p>Analiza zapytań, odbiorców, kreacji, kosztów i jakości ruchu. Zmiany w kampanii oraz rekomendacje dla oferty lub strony.</p></article>
          <article class="scope-card reveal" data-delay="1"><span class="scope-num">06 / miejsce docelowe</span><div class="scope-icon"><svg viewBox="0 0 24 24"><path d="M3 5h18v14H3zM3 9h18M7 7h.01M10 7h.01"/></svg></div><h3>Landing page i UX</h3><p>Możemy zaprojektować lub poprawić stronę docelową, formularz, CTA i ścieżkę, do której trafia użytkownik.</p></article>
        </div>
      </div>
    </section>

    <section class="team-section">
      <img class="team-signet" src="<?php echo esc_url($zp_campaign_asset_url); ?>zp-sygnet-ciemny.webp" alt="" aria-hidden="true" loading="lazy" decoding="async">
      <div class="wrap team-inner">
        <div class="team-copy reveal">
          <p class="section-kicker section-kicker--light"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Jeden zespół • pełny obraz</p>
          <h2>Kampania nie żyje <strong class="gradient-text">w osobnym silosie.</strong></h2>
          <p>W jednym zespole łączymy reklamę, projekt, treść, stronę i techniczne wdrożenie. Dzięki temu problem nie krąży między pięcioma wykonawcami — tylko trafia tam, gdzie naprawdę można go rozwiązać.</p>
          <div class="team-chips"><span>Meta Ads</span><span>Google Ads</span><span>UX/UI</span><span>WordPress</span><span>branding</span><span>analityka</span></div>
          <a class="btn btn-primary" href="/kontakt/">Poznajmy Twój cel <svg viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
        </div>
        <img class="team-visual" src="<?php echo esc_url($zp_campaign_asset_url); ?>team-kontakt.webp" alt="Zespół Zaprojektowani — strategia, kampanie i wdrożenie" loading="lazy" decoding="async">
        <div class="team-float-note"><span>Model współpracy</span><strong>Strategia, kreacja, technologia i pomiar przy jednym stole.</strong></div>
      </div>
    </section>

    <section class="section faq" id="faq">
      <div class="wrap faq-layout">
        <div class="faq-intro reveal"><div class="faq-figure" aria-hidden="true"><img src="<?php echo esc_url($zp_campaign_asset_url); ?>mateusz-pointing.webp" alt="" loading="lazy" decoding="async"></div><p class="section-kicker"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>FAQ / kampanie reklamowe</p><h2 class="section-title">Zanim klikniesz <strong>„uruchom”.</strong></h2><p class="section-lead">Konkretnie o współpracy, budżecie, materiałach, czasie i tym, czego potrzebujemy na start.</p></div>
        <div class="faq-list reveal" data-delay="1">
          <details class="faq-item"><summary><span class="faq-no">01</span><span>Czy zajmujecie się jednocześnie Meta Ads i Google Ads?</span><i class="faq-plus"></i></summary><div class="faq-answer">Tak. Kanały dobieramy do celu i ścieżki klienta. W wielu projektach <strong>Google przechwytuje aktywną intencję</strong>, a Meta buduje zainteresowanie i wspiera powroty. Nie zawsze trzeba uruchamiać wszystko naraz — zakres powinien wynikać ze strategii.</div></details>
          <details class="faq-item"><summary><span class="faq-no">02</span><span>Jaki budżet reklamowy jest potrzebny na start?</span><i class="faq-plus"></i></summary><div class="faq-answer">To zależy od branży, lokalizacji, konkurencji, wartości klienta i liczby testowanych kampanii. Najpierw określamy, <strong>co chcemy sprawdzić i ile danych potrzeba do sensownej oceny</strong>. Dopiero wtedy proponujemy budżet — bez obietnic oderwanych od realiów.</div></details>
          <details class="faq-item"><summary><span class="faq-no">03</span><span>Czy przygotowujecie grafiki i teksty reklam?</span><i class="faq-plus"></i></summary><div class="faq-answer">Tak. Możemy przygotować kierunek kreatywny, formaty statyczne i animowane, copy, nagłówki oraz warianty do testów. Jeżeli masz własne materiały, selekcjonujemy je i adaptujemy do kanału.</div></details>
          <details class="faq-item"><summary><span class="faq-no">04</span><span>Czy możecie poprawić stronę, na którą kieruje reklama?</span><i class="faq-plus"></i></summary><div class="faq-answer">Tak — i często ma to większy wpływ niż kolejna drobna zmiana w kampanii. Projektujemy strony i sklepy, więc możemy poprawić <strong>hierarchię oferty, CTA, formularz, wersję mobilną, szybkość działania oraz pomiar</strong> albo przygotować dedykowany landing page.</div></details>
          <details class="faq-item"><summary><span class="faq-no">05</span><span>Po jakim czasie można oceniać kampanię?</span><i class="faq-plus"></i></summary><div class="faq-answer">Nie ma jednego terminu dla każdej branży. Wpływają na niego budżet, liczba konwersji, długość procesu decyzyjnego, sezonowość i jakość istniejących danych. Na początku patrzymy na sygnały pośrednie, a pełniejszą ocenę opieramy na odpowiednio dużej próbce.</div></details>
          <details class="faq-item"><summary><span class="faq-no">06</span><span>Czy gwarantujecie konkretny wynik?</span><i class="faq-plus"></i></summary><div class="faq-answer">Nie składamy gwarancji, których nie da się uczciwie kontrolować. Możemy zagwarantować <strong>przejrzysty proces, pomiar, regularną optymalizację i jasne wnioski</strong>. Wynik zależy także od oferty, rynku, ceny, sezonu, strony i obsługi zapytań.</div></details>
          <details class="faq-item"><summary><span class="faq-no">07</span><span>Czy pracujecie tylko z firmami z Katowic?</span><i class="faq-plus"></i></summary><div class="faq-answer">Nie. Jesteśmy z Katowic, ale kampanie prowadzimy dla firm ze Śląska i całej Polski. Spotkania i raportowanie mogą odbywać się zdalnie, a przy projektach lokalnych wykorzystujemy znajomość regionu i specyfiki lokalnego popytu.</div></details>
          <details class="faq-item"><summary><span class="faq-no">08</span><span>Ile kosztuje prowadzenie kampanii reklamowych?</span><i class="faq-plus"></i></summary><div class="faq-answer">Na koszt składają się dwie rzeczy: <strong>budżet mediowy</strong> (płacony bezpośrednio Google i Meta) oraz <strong>koszt obsługi</strong> po naszej stronie. Budżet mediowy dobieramy do branży, konkurencji i celu, a zakres obsługi — do wybranego pakietu. Dokładną wycenę przygotujemy w Studio Wyceny po poznaniu Twojej sytuacji.</div></details>
          <details class="faq-item"><summary><span class="faq-no">09</span><span>Czym różni się Google Ads od Meta Ads (Facebook i Instagram)?</span><i class="faq-plus"></i></summary><div class="faq-answer"><strong>Google Ads</strong> przechwytuje aktywną intencję — pojawia się, gdy ktoś sam szuka usługi, produktu lub firmy. <strong>Meta Ads</strong> (Facebook i Instagram) buduje zainteresowanie obrazem i historią oraz wraca do osób, które poznały markę. W większości projektów najlepiej działają razem — każdy kanał odpowiada za inny moment decyzji.</div></details>
          <details class="faq-item"><summary><span class="faq-no">10</span><span>Czy prowadzicie kampanie dla sklepów internetowych i e-commerce?</span><i class="faq-plus"></i></summary><div class="faq-answer">Tak. Dla sklepów uruchamiamy kampanie produktowe (Performance Max / katalog), remarketing dynamiczny i kampanie w Meta oparte na katalogu. Konfigurujemy pomiar sprzedaży (Pixel, CAPI, wartości konwersji), żeby optymalizować kampanie pod realny przychód i ROAS, a nie tylko kliknięcia.</div></details>
          <details class="faq-item"><summary><span class="faq-no">11</span><span>Jak wygląda raportowanie i mierzenie wyników kampanii?</span><i class="faq-plus"></i></summary><div class="faq-answer">Na starcie porządkujemy analitykę: konwersje, zdarzenia, UTM i połączenie z GA4. Raportujemy <strong>koszt pozyskania zapytania, jakość ruchu, przychód przypisany kampaniom i ROAS</strong> — a nie same wyświetlenia. Zależy nam, żeby było jasno widać, co reklama realnie przynosi firmie.</div></details>
          <details class="faq-item"><summary><span class="faq-no">12</span><span>Po jakim czasie widać pierwsze efekty kampanii?</span><i class="faq-plus"></i></summary><div class="faq-answer">Pierwsze dane (kliknięcia, koszt kontaktu, jakość ruchu) pojawiają się już w pierwszych dniach, ale wiarygodną ocenę opieramy na odpowiednio dużej próbce. Kampanie w Google przy wysokiej intencji potrafią dać zapytania szybciej; budowanie popytu w Meta oraz dłuższe procesy decyzyjne wymagają więcej czasu na optymalizację.</div></details>
        </div>
      </div>
    </section>

    <section class="final-cta" id="kontakt">
      <img class="final-signet" src="<?php echo esc_url($zp_campaign_asset_url); ?>zp-sygnet-ciemny.webp" alt="" aria-hidden="true" loading="lazy" decoding="async">
      <div class="wrap final-inner">
        <div class="final-copy reveal">
          <p class="section-kicker section-kicker--light"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>Następny krok</p>
          <h2>Powiedz, co ma się wydarzyć. <strong class="gradient-text">Zaprojektujemy drogę.</strong></h2>
          <p>Opisz ofertę, dotychczasowe działania i cel kampanii. W Studio Wyceny wybierzesz punkt startu, a my dopasujemy kanały, tempo testów i zakres obsługi do Twojej sytuacji.</p>
          <div class="hero-actions"><a class="btn btn-primary" href="https://zaprojektowani.com/studio-wyceny/?zpbs_service=ads#zpbsUltimate">Przejdź do Studia Wyceny <svg viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg></a><a class="btn btn-ghost" href="tel:+48501054253">Zadzwoń: 501 054 253 <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92z"/></svg></a></div>
          <p class="cta-microcopy">Dzwonimy w godzinach <strong>9:00–17:00</strong> i zwykle oddzwaniamy do 15 minut. Napisz też przez <a href="https://zaprojektowani.com/kontakt/">formularz kontaktowy</a>.</p>
        </div>
        <img class="final-team" src="<?php echo esc_url($zp_campaign_asset_url); ?>wiedza-ekipa.webp" alt="Zespół Zaprojektowani analizuje projekt kampanii" loading="lazy" decoding="async">
        <div class="final-contact"><i><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92z"/></svg></i><div><span>Porozmawiajmy</span><strong>501 054 253</strong></div></div>
      </div>
    </section>
<?php /* v2.2.688: wspólny formularz kontaktowy — ten sam co na logo-branding-katowice
         i strony-internetowe-katowice. Dzielony globalnie, więc zmiana w jednym
         miejscu obejmuje wszystkie podstrony usługowe. */ ?>
<!-- ZP Suite / globalna sekcja kontakt pod FAQ --><?php echo do_shortcode('[zp_contact_system]'); ?>
  </main>
</div>

<?php echo $zp_logo_thumb_fix(do_shortcode('[zp_footer]')); ?>
<?php wp_footer(); ?>
</body>
</html>
