<!-- =========================================================
ZAPROJEKTOWANI — CONTACT SYSTEM / LIGHT PREMIUM v1.2 MOBILE STABLE
Zaprojektowani Suite template / v0.2 assets moved to /assets/css/blocks and /assets/js/blocks

v1.2:
- mobile stable: wyłączone outro/blur/chowanie na mobile
- mobile: wyłączone pływanie całego panelu kontaktu, żeby nie skakało przy scrollowaniu
- desktop: outro startuje później i jest dużo subtelniejsze
- reveal na mobile lżejszy, bez dużych transformów i blurów
- zachowane: wybór usług, zakładki, WhatsApp, formularz demo, lokalny Lucide
========================================================= -->

<?php
$zp_contact_heading = zp_suite_opt('contact.heading_html', 'Opowiedz nam o projekcie — <span>dobierzemy zakres</span> i <strong>najlepszą ścieżkę działania</strong>.');
$zp_contact_lead = zp_suite_opt('contact.lead', '');
$zp_contact_privacy = zp_suite_opt('contact.privacy_text', 'Wyrażam zgodę na kontakt w sprawie przesłanego zapytania.');
?>
<section class="zpContactSystemLight" id="zpContactSystemLight" data-zp-contact-system-light aria-labelledby="zpContactSystemLightTitle">
<div class="zpContactSystemLight__bleed" aria-hidden="true"></div>
  <div class="zpContactSystemLight__mark" aria-hidden="true">contact</div>

  <div class="zpContactSystemLight__inner">

    <header class="zpContactSystemLight__head">
      <span class="zpContactSystemLight__eyebrow">Szybkie zapytanie</span>

      <h2 id="zpContactSystemLightTitle"><?php echo wp_kses($zp_contact_heading, zp_suite_allowed_html()); ?></h2>

      <p><?php echo esc_html($zp_contact_lead); ?></p>
    </header>

    <div class="zpContactSystemLight__layout">

      <aside class="zpContactSystemLight__services" aria-label="Wybór usług">
        <div class="zpContactSystemLight__sideTitle">
          <span>01</span>
          <div>
            <strong>Wybierz zakres</strong>
            <p>Możesz zaznaczyć jedną lub kilka usług.</p>
          </div>
        </div>

        <div class="zpContactSystemLight__grid">

          <button class="zpContactSystemLight__service is-active" type="button" data-service="Strona internetowa">
            <i data-lucide="monitor"></i>
            <span class="zpContactSystemLight__serviceCheck"><i data-lucide="check"></i></span>
            <em>WWW</em>
            <strong>Strony internetowe</strong>
            <p>Strona firmowa, landing page, WordPress, UX, mobile i SEO-ready.</p>
          </button>

          <button class="zpContactSystemLight__service" type="button" data-service="Sklep internetowy">
            <i data-lucide="shopping-cart"></i>
            <span class="zpContactSystemLight__serviceCheck"><i data-lucide="check"></i></span>
            <em>E-commerce</em>
            <strong>Sklepy WooCommerce</strong>
            <p>Produkty, koszyk, płatności, dostawy, analityka i fundament pod reklamy.</p>
          </button>

          <button class="zpContactSystemLight__service is-active" type="button" data-service="Logo i branding">
            <i data-lucide="pen-tool"></i>
            <span class="zpContactSystemLight__serviceCheck"><i data-lucide="check"></i></span>
            <em>Start marki</em>
            <strong>Logo &amp; Branding</strong>
            <p>Logo, identyfikacja, kolorystyka, typografia i spójny system marki.</p>
          </button>

          <button class="zpContactSystemLight__service" type="button" data-service="Meta Ads">
            <i data-lucide="megaphone"></i>
            <span class="zpContactSystemLight__serviceCheck"><i data-lucide="check"></i></span>
            <em>Sprzedaż</em>
            <strong>Meta Ads</strong>
            <p>Kampanie Facebook / Instagram, kreacje, Pixel, CAPI i testy reklam.</p>
          </button>

          <button class="zpContactSystemLight__service" type="button" data-service="SEO">
            <i data-lucide="search-check"></i>
            <span class="zpContactSystemLight__serviceCheck"><i data-lucide="check"></i></span>
            <em>Widoczność</em>
            <strong>SEO i treści</strong>
            <p>Struktura, techniczne SEO, frazy, artykuły i widoczność w Google.</p>
          </button>

          <button class="zpContactSystemLight__service" type="button" data-service="Inne">
            <i data-lucide="settings-2"></i>
            <span class="zpContactSystemLight__serviceCheck"><i data-lucide="check"></i></span>
            <em>Inne</em>
            <strong>Inne</strong>
            <p>Masz niestandardowy temat? Opisz projekt, a dobierzemy zakres i najlepszy kontakt.</p>
          </button>

        </div>
      </aside>

      <main class="zpContactSystemLight__contact" aria-label="Forma kontaktu">
        <div class="zpContactSystemLight__contactTop">
          <div class="zpContactSystemLight__sideTitle">
            <span>02</span>
            <div>
              <strong>Jak mamy się skontaktować?</strong>
              <p>Wybierz najwygodniejszą formę rozmowy.</p>
            </div>
          </div>

          <div class="zpContactSystemLight__selected">
            <span>Wybrane usługi</span>
            <strong data-zp-contact-selected>Strona internetowa, Logo i branding</strong>
          </div>
        </div>

        <div class="zpContactSystemLight__tabs" role="tablist" aria-label="Wybór formy kontaktu">
          <button class="zpContactSystemLight__tab is-active" type="button" role="tab" aria-selected="true" aria-controls="zpContactPanelForm" id="zpContactTabForm" data-contact-tab="form">
            <i data-lucide="clipboard-list"></i>
            <strong>Formularz</strong>
            <span></span>
          </button>

          <button class="zpContactSystemLight__tab" type="button" role="tab" aria-selected="false" aria-controls="zpContactPanelPhone" id="zpContactTabPhone" data-contact-tab="phone">
            <i data-lucide="phone-call"></i>
            <strong>Oddzwońcie</strong>
            <span></span>
          </button>

          <button class="zpContactSystemLight__tab" type="button" role="tab" aria-selected="false" aria-controls="zpContactPanelWhatsapp" id="zpContactTabWhatsapp" data-contact-tab="whatsapp">
            <i data-lucide="message-circle"></i>
            <strong>WhatsApp</strong>
            <span></span>
          </button>
        </div>

        <div class="zpContactSystemLight__panelWrap">

          <section class="zpContactSystemLight__panel is-active" id="zpContactPanelForm" role="tabpanel" aria-labelledby="zpContactTabForm" data-contact-panel="form">
            <div class="zpContactSystemLight__panelHead">
              <span>Formularz kontaktowy</span>
              <h3>Opisz projekt, a my wrócimy z konkretną propozycją.</h3>
            </div>

            <form class="zpContactSystemLight__form" data-zp-contact-form>
              <input type="hidden" name="zp_nonce" value="<?php echo esc_attr(wp_create_nonce('zp_suite_contact')); ?>">
              <div class="zpContactSystemLight__fields">
                <label>
                  <span>Imię</span>
                  <input type="text" name="name" placeholder="Imię" autocomplete="name">
                </label>

                <label>
                  <span>Telefon</span>
                  <input type="tel" name="phone" placeholder="Numer telefonu" autocomplete="tel">
                </label>

                <label class="zpContactSystemLight__full">
                  <span>E-mail</span>
                  <input type="email" name="email" placeholder="Adres e-mail" autocomplete="email">
                </label>

                <label class="zpContactSystemLight__full">
                  <span>Opis projektu</span>
                  <textarea name="message" placeholder="Opisz krótko, czego potrzebujesz..."></textarea>
                </label>
              </div>

              <label class="zpContactSystemLight__consent">
                <input type="checkbox" name="consent" value="1">
                <span class="zpContactSystemLight__box"><i data-lucide="check"></i></span>
                <span class="zpContactSystemLight__consentText">
                  <?php echo esc_html($zp_contact_privacy); ?>
                </span>
              </label>

              <button class="zpContactSystemLight__btn" type="submit">
                Wyślij
                <i data-lucide="arrow-up-right"></i>
              </button>

              <p class="zpContactSystemLight__notice" data-zp-contact-notice>
                Wiadomość zostanie wysłana bezpośrednio do zespołu Zaprojektowani.com.
              </p>
            </form>
          </section>

          <section class="zpContactSystemLight__panel" id="zpContactPanelPhone" role="tabpanel" aria-labelledby="zpContactTabPhone" data-contact-panel="phone" hidden>
            <div class="zpContactSystemLight__panelHead">
              <span>Kontakt telefoniczny</span>
              <h3>Zostaw numer — oddzwonimy i ustalimy najlepszy kierunek.</h3>
            </div>

            <div class="zpContactSystemLight__fields">
              <label>
                <span>Dzień</span>
                <span class="zpContactSystemLight__selectWrap">
                  <select>
                    <option>Dzisiaj</option>
                    <option>Jutro</option>
                    <option>W tym tygodniu</option>
                    <option>W przyszłym tygodniu</option>
                  </select>
                  <i data-lucide="chevron-down"></i>
                </span>
              </label>

              <label>
                <span>Godzina</span>
                <span class="zpContactSystemLight__selectWrap">
                  <select>
                    <option>09:00–11:00</option>
                    <option>11:00–13:00</option>
                    <option>13:00–15:00</option>
                    <option>15:00–17:00</option>
                  </select>
                  <i data-lucide="chevron-down"></i>
                </span>
              </label>

              <label class="zpContactSystemLight__full">
                <span>Imię lub firma</span>
                <input type="text" name="name" placeholder="Jak mamy się zwracać?" autocomplete="name" data-zp-phone-name>
              </label>

              <label class="zpContactSystemLight__full">
                <span>Telefon</span>
                <input type="tel" name="phone" placeholder="Numer telefonu" autocomplete="tel" data-zp-phone-input>
              </label>

              <label class="zpContactSystemLight__full">
                <span>Notatka</span>
                <textarea name="message" placeholder="Napisz krótko, czego dotyczy projekt..." data-zp-phone-message></textarea>
              </label>
            </div>

            <div class="zpContactSystemLight__phoneCtaBreak"></div>
            <button class="zpContactSystemLight__btn" type="button" data-zp-phone-request>
              Poproś o telefon
              <i data-lucide="phone-call"></i>
            </button>

            <p class="zpContactSystemLight__notice">
              Najczęściej oddzwaniamy jeszcze tego samego dnia roboczego.
            </p>
          </section>

          <section class="zpContactSystemLight__panel" id="zpContactPanelWhatsapp" role="tabpanel" aria-labelledby="zpContactTabWhatsapp" data-contact-panel="whatsapp" hidden>
            <div class="zpContactSystemLight__wa">
              <span class="zpContactSystemLight__waIcon">
                <i data-lucide="message-circle"></i>
              </span>

              <div class="zpContactSystemLight__panelHead">
                <span>Najkrótsza ścieżka</span>
                <h3>Napisz do nas od razu na WhatsApp.</h3>
                <p>
                  Otworzymy gotową wiadomość z wybranym zakresem. Dopisz szczegóły,
                  wyślij link do obecnej strony albo podeślij inspiracje.
                </p>
              </div>

              <div class="zpContactSystemLight__selected zpContactSystemLight__selected--wa">
                <span>Do wiadomości dodamy</span>
                <strong data-zp-contact-selected-wa>Strona internetowa, Logo i branding</strong>
              </div>

              <a class="zpContactSystemLight__btn zpContactSystemLight__btn--wa" href="#" target="_blank" rel="noopener" data-zp-contact-wa>
                Napisz na WhatsApp
                <i data-lucide="message-circle"></i>
              </a>
            </div>
          </section>

        </div>
      </main>

    </div>

    <div class="zpContactSystemLight__footer">
      <div class="zpContactSystemLight__brand">
        <img
          src="https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp"
          alt="Zaprojektowani.com"
          loading="lazy"
          decoding="async"
        >
      </div>

      <p>
        Nie musisz mieć gotowego briefu. Wystarczy ogólny pomysł — pomożemy doprecyzować zakres,
        priorytety, technologię i kolejność działań.
      </p>

      <a href="/kontakt/">
        Pełna strona kontaktu
        <i data-lucide="arrow-right"></i>
      </a>
    </div>

  </div>


  <div class="zpContactModal" data-zp-contact-modal aria-hidden="true" hidden>
    <div class="zpContactModal__backdrop" data-zp-contact-modal-close></div>
    <div class="zpContactModal__box" role="dialog" aria-modal="true" aria-live="polite">
      <button class="zpContactModal__close" type="button" data-zp-contact-modal-close aria-label="Zamknij komunikat">×</button>
      <span class="zpContactModal__icon"><i data-lucide="sparkles"></i></span>
      <h3 data-zp-contact-modal-title>Komunikat</h3>
      <div data-zp-contact-modal-text></div>
      <button class="zpContactModal__btn" type="button" data-zp-contact-modal-close>Rozumiem</button>
    </div>
  </div>
</section>

<!-- =========================================================
/ZAPROJEKTOWANI — CONTACT SYSTEM / LIGHT PREMIUM v1.2 MOBILE STABLE
========================================================= -->
