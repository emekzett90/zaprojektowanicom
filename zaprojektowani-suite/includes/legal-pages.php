<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.1.18 — bezpieczne tworzenie stron prawnych.
 * - działa na aktywacji i w admin_init,
 * - nie uruchamia się na froncie,
 * - aktualizuje istniejące strony po slugu zamiast tworzyć duplikaty,
 * - zapisuje też poprawny Elementor HTML widget, ale zostawia klasyczną treść jako fallback.
 */

function zp_suite_legal_company_data(){
  return [
    'company' => 'AP Studio',
    'nip' => '9930682613',
    'address' => 'ul. Modelarska 18/2, 40-142 Katowice',
    'phone' => '+48 501 054 253',
    'phone_raw' => '48501054253',
    'email' => 'kontakt@zaprojektowani.com',
    'site' => 'zaprojektowani.com',
  ];
}

function zp_suite_legal_css(){
  return '<style id="zp-legal-pages-style">.zpLegal{--ink:#071426;--muted:#5f6b7d;--line:rgba(7,20,38,.12);--soft:#f5f6f8;--navy:#071426;--navy2:#102a4f;--blue:#1c477a;font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--ink);background:#fff;position:relative;overflow:hidden;padding:clamp(76px,8vw,132px) 0 clamp(72px,7vw,116px)}.zpLegal:before{content:"";position:absolute;inset:0 0 auto 0;height:420px;background:linear-gradient(180deg,#f7f8fa 0%,#fff 100%);pointer-events:none}.zpLegal__wrap{width:min(1120px,calc(100% - 36px));margin:0 auto;position:relative;z-index:2}.zpLegal__hero{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:clamp(28px,5vw,70px);align-items:end;margin-bottom:clamp(34px,5vw,68px)}.zpLegal__kicker{display:inline-flex;align-items:center;gap:10px;margin:0 0 16px;color:rgba(7,20,38,.62);font-size:11px;line-height:1;text-transform:uppercase;letter-spacing:.18em;font-weight:800}.zpLegal__kicker:before{content:"";width:28px;height:1px;background:linear-gradient(90deg,var(--navy2),var(--blue))}.zpLegal h1{margin:0;color:var(--ink);font-size:clamp(36px,6vw,74px);line-height:1.04;letter-spacing:-.052em;font-weight:680;text-wrap:balance}.zpLegal__lead{max-width:760px;margin:46px 0 0;color:var(--muted);font-size:clamp(16px,1.5vw,20px);line-height:1.82;letter-spacing:-.01em}.zpLegal__meta{border:1px solid var(--line);border-radius:28px;background:rgba(255,255,255,.78);box-shadow:0 24px 70px rgba(7,20,38,.08);padding:22px}.zpLegal__meta span{display:block;color:rgba(7,20,38,.54);font-size:10px;text-transform:uppercase;letter-spacing:.16em;font-weight:800;margin-bottom:8px}.zpLegal__meta strong{display:block;font-size:15px;line-height:1.45;color:var(--ink)}.zpLegal__layout{display:grid;grid-template-columns:280px minmax(0,1fr);gap:clamp(26px,5vw,64px);align-items:start}.zpLegal__toc{position:sticky;top:104px;border:1px solid var(--line);border-radius:26px;background:#fff;padding:18px;box-shadow:0 18px 50px rgba(7,20,38,.055)}.zpLegal__toc strong{display:block;margin:0 0 12px;color:var(--ink);font-size:13px;letter-spacing:.06em;text-transform:uppercase}.zpLegal__toc a{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 12px;border-radius:999px;color:#344054;text-decoration:none;font-size:13px;font-weight:650}.zpLegal__toc a:hover{background:#f1f3f6;color:#071426}.zpLegal__content{display:grid;gap:18px}.zpLegal__section{border:1px solid var(--line);border-radius:30px;background:#fff;padding:clamp(24px,3.4vw,42px);box-shadow:0 18px 52px rgba(7,20,38,.045)}.zpLegal h2{margin:0 0 15px;color:var(--ink);font-size:clamp(24px,3vw,38px);line-height:1.05;letter-spacing:-.04em;font-weight:680}.zpLegal h3{margin:24px 0 10px;color:var(--ink);font-size:20px;line-height:1.2;letter-spacing:-.025em}.zpLegal p,.zpLegal li{color:var(--muted);font-size:15px;line-height:1.75;letter-spacing:-.005em}.zpLegal p{margin:0 0 12px}.zpLegal ul,.zpLegal ol{margin:10px 0 0;padding-left:22px}.zpLegal li{margin:6px 0}.zpLegal a{color:#102a4f;font-weight:760;text-decoration:none}.zpLegal a:hover{text-decoration:underline}.zpLegal__note{border-radius:22px;background:linear-gradient(135deg,#071426,#102a4f,#1c477a);color:#fff;padding:22px;margin-top:18px}.zpLegal__note p,.zpLegal__note li{color:rgba(255,255,255,.78)}.zpLegal__note strong{color:#fff}.zpLegal__chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:16px}.zpLegal__chips span{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:8px 12px;color:#344054;background:#f7f8fa;font-size:12px;font-weight:700}.zpLegal__updated{margin-top:24px;color:rgba(7,20,38,.48);font-size:12px}.zpLegal__cta{margin-top:22px;display:flex;flex-wrap:wrap;gap:10px}.zpLegal__btn{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 18px;border-radius:999px;border:1px solid #071426;background:#fff;color:#071426!important;text-decoration:none!important;font-size:13px;font-weight:760}.zpLegal__btn:hover{background:#071426;color:#fff!important;text-decoration:none!important}@media(max-width:900px){.zpLegal{padding-top:92px}.zpLegal__hero,.zpLegal__layout{grid-template-columns:1fr}.zpLegal__toc{position:relative;top:auto}.zpLegal__meta{max-width:440px}.zpLegal h1{font-size:clamp(34px,11vw,52px);line-height:1.06}.zpLegal__lead{margin-top:34px}}</style>';
}

function zp_suite_legal_shell($title, $lead, $sections){
  $d = zp_suite_legal_company_data();
  $toc = '';
  $body = '';
  foreach ($sections as $i => $sec) {
    $id = sanitize_title($sec['title']);
    $toc .= '<a href="#'.$id.'"><span>'.esc_html($sec['title']).'</span><b>↘</b></a>';
    $body .= '<section class="zpLegal__section" id="'.$id.'"><h2>'.esc_html($sec['title']).'</h2>'.$sec['html'].'</section>';
  }
  return zp_suite_legal_css().'<article class="zpLegal"><div class="zpLegal__wrap"><header class="zpLegal__hero"><div><span class="zpLegal__kicker">Dokumenty prawne</span><h1>'.esc_html($title).'</h1><p class="zpLegal__lead">'.esc_html($lead).'</p><div class="zpLegal__cta"><a class="zpLegal__btn" href="mailto:'.$d['email'].'">'.$d['email'].'</a><a class="zpLegal__btn" href="tel:+'.$d['phone_raw'].'">'.$d['phone'].'</a></div></div><aside class="zpLegal__meta"><span>Dane firmy</span><strong>'.$d['company'].'<br>NIP: '.$d['nip'].'<br>'.$d['address'].'<br>'.$d['email'].'<br>'.$d['phone'].'</strong></aside></header><div class="zpLegal__layout"><nav class="zpLegal__toc" aria-label="Spis treści"><strong>Na tej stronie</strong>'.$toc.'</nav><main class="zpLegal__content">'.$body.'<p class="zpLegal__updated">Ostatnia aktualizacja: maj 2026 r.</p></main></div></div></article>';
}

function zp_suite_legal_pages_definitions(){
  $d = zp_suite_legal_company_data();
  $mail = '<a href="mailto:'.$d['email'].'">'.$d['email'].'</a>';
  return [
    'regulamin' => [
      'title' => 'Regulamin świadczenia usług',
      'lead' => 'Zasady współpracy z AP Studio / Zaprojektowani.com przy projektach stron internetowych, sklepów, identyfikacji wizualnej, kampanii reklamowych, SEO i usług kreatywnych.',
      'sections' => [
        ['title'=>'Postanowienia ogólne','html'=>'<p>Regulamin określa ogólne zasady korzystania ze strony zaprojektowani.com oraz ramowe warunki współpracy przy usługach kreatywnych, technologicznych i marketingowych świadczonych przez AP Studio.</p><p>Usługodawcą jest <strong>AP Studio</strong><br><strong>NIP: 9930682613</strong><br>Adres do korespondencji: <strong>ul. Modelarska 18/2, 40-142 Katowice</strong>.</p><p>Dokument ma charakter informacyjny i porządkujący. Szczegółowe ustalenia konkretnego projektu — zakres, cena, terminy, liczba tur poprawek, forma przekazania materiałów oraz warunki płatności — mogą wynikać z oferty, korespondencji mailowej, briefu, zamówienia, faktury pro forma lub osobnej umowy.</p><p>Korzystanie ze strony, wysłanie formularza kontaktowego albo rozpoczęcie korespondencji nie oznacza automatycznego zawarcia umowy. Umowa lub zamówienie dochodzi do skutku dopiero po zaakceptowaniu warunków współpracy przez obie strony.</p>'],
        ['title'=>'Zakres usług','html'=>'<p>Oferta może obejmować w szczególności: projektowanie stron internetowych, sklepów WooCommerce, landing page, logo, branding, materiały graficzne, konfigurację kampanii reklamowych, SEO, copywriting, integracje, analitykę, optymalizację techniczną oraz opiekę nad stroną.</p><p>W zależności od wybranego zakresu projekt może obejmować także przygotowanie architektury informacji, makiet, projektów UI, wdrożenie w WordPress lub Elementorze, konfigurację formularzy, płatności, automatyzacji, podstawowych zabezpieczeń, przekierowań, analityki, pikseli reklamowych, optymalizację szybkości oraz przygotowanie strony do działań SEO i reklamowych.</p><p>Elementy niestandardowe, takie jak konfiguratory, systemy wycen, integracje z zewnętrznymi narzędziami, migracje treści, rozbudowane sklepy, wersje językowe, zaawansowane animacje, customowe wtyczki lub importy produktów, są każdorazowo wyceniane indywidualnie.</p><div class="zpLegal__chips"><span>strony WWW</span><span>sklepy internetowe</span><span>branding</span><span>SEO</span><span>Meta Ads / Google Ads</span></div>'],
        ['title'=>'Kontakt i wycena','html'=>'<p>Zapytania można kierować przez formularze na stronie, telefonicznie pod numerem <a href="tel:+48501054253">+48 501 054 253</a> lub mailowo: '.$mail.'.</p><p>Wycena jest przygotowywana indywidualnie na podstawie zakresu, materiałów, funkcji, terminów oraz poziomu złożoności projektu. Do przygotowania rzetelnej wyceny możemy poprosić o uzupełnienie briefu, przesłanie linków referencyjnych, informacji o branży, listy podstron, funkcji sklepu, liczby produktów, oczekiwań względem SEO lub kampanii reklamowych.</p><p>Wycena może mieć charakter orientacyjny, jeżeli na etapie zapytania nie są znane wszystkie wymagania techniczne. W takim przypadku ostateczny koszt może zostać doprecyzowany po analizie materiałów i zakresu prac.</p>'],
        ['title'=>'Realizacja projektu','html'=>'<p>Realizacja może obejmować etapy takie jak analiza potrzeb, brief, projekt graficzny, copywriting, wdrożenie, testy, poprawki, publikacja oraz przekazanie dostępu. Szczegółowy harmonogram i zakres ustalane są indywidualnie w ofercie, briefie, mailu lub umowie.</p><p>Klient zobowiązuje się dostarczyć materiały niezbędne do realizacji, w tym treści, zdjęcia, dane firmy, dostępy techniczne oraz uwagi do projektu w uzgodnionych terminach. Opóźnienie w przekazaniu materiałów lub akceptacji etapów może wpłynąć na termin realizacji.</p><p>Poprawki realizowane są w zakresie uzgodnionym dla danego projektu. Zmiany wykraczające poza zaakceptowaną koncepcję, zmianę struktury po etapie akceptacji, dopisywanie nowych funkcji lub rozbudowę zakresu mogą wymagać osobnej wyceny.</p><p>Po zakończeniu prac i publikacji strony klient powinien zweryfikować poprawność danych, treści, linków, formularzy i elementów kontaktowych. Ewentualne uwagi wdrożeniowe należy zgłosić niezwłocznie po przekazaniu projektu.</p>'],
        ['title'=>'Płatności','html'=>'<p>Warunki płatności, zaliczki, płatności etapowe oraz termin płatności faktur są określane indywidualnie dla danego projektu. Rozpoczęcie prac może być uzależnione od zaksięgowania zaliczki.</p><p>W przypadku projektów etapowych płatności mogą być powiązane z rozpoczęciem prac, akceptacją projektu graficznego, wdrożeniem wersji testowej, publikacją strony lub przekazaniem plików. Brak płatności w terminie może skutkować wstrzymaniem prac, przekazania dostępów lub publikacji projektu.</p><p>Ceny podawane w ofertach mogą być cenami netto albo brutto — zgodnie z treścią konkretnej oferty lub dokumentu sprzedaży. Zakres świadczeń dodatkowych, utrzymania strony, hostingu, licencji, płatnych wtyczek lub kampanii reklamowych może być rozliczany osobno.</p>'],
        ['title'=>'Prawa autorskie','html'=>'<p>Przeniesienie majątkowych praw autorskich lub udzielenie licencji następuje w zakresie ustalonym indywidualnie, najczęściej po opłaceniu całości wynagrodzenia. Do czasu pełnego rozliczenia projektu materiały mogą pozostawać własnością wykonawcy.</p><p>Elementy pochodzące z narzędzi zewnętrznych, banków zdjęć, fontów, wtyczek, motywów, bibliotek, integracji lub systemów SaaS mogą podlegać osobnym licencjom dostawców. Klient otrzymuje prawa wyłącznie w zakresie, w jakim może ich udzielić wykonawca.</p><p>AP Studio może prezentować zrealizowany projekt w portfolio, mediach społecznościowych, case studies i materiałach ofertowych, chyba że strony ustalą inaczej na piśmie.</p>'],
        ['title'=>'Reklamacje i kontakt','html'=>'<p>Uwagi dotyczące realizacji usług można zgłaszać mailowo na adres '.$mail.'. Każde zgłoszenie jest analizowane indywidualnie w odniesieniu do ustalonego zakresu prac.</p><div class="zpLegal__note"><p><strong>Ważne:</strong> szczegółowe warunki konkretnego projektu, jeżeli zostały ustalone mailowo, w ofercie lub umowie, mają pierwszeństwo przed ogólnymi zapisami tego regulaminu.</p></div>'],
      ],
    ],
    'polityka-prywatnosci' => [
      'title' => 'Polityka prywatności',
      'lead' => 'Informacje o tym, jakie dane osobowe możemy przetwarzać, w jakim celu i jakie prawa przysługują osobom kontaktującym się z AP Studio / Zaprojektowani.com.',
      'sections' => [
        ['title'=>'Administrator danych','html'=>'<p>Administratorem danych osobowych jest <strong>AP Studio</strong>, NIP: <strong>9930682613</strong>, adres do korespondencji: <strong>ul. Modelarska 18/2, 40-142 Katowice</strong>. Kontakt w sprawach danych osobowych: '.$mail.'.</p>'],
        ['title'=>'Zakres przetwarzanych danych','html'=>'<p>Możemy przetwarzać dane podane dobrowolnie w formularzach, wiadomościach e-mail, rozmowach telefonicznych lub briefach, takie jak imię i nazwisko, nazwa firmy, adres e-mail, numer telefonu, adres strony, informacje o projekcie, pliki przekazane do wyceny oraz dane potrzebne do wystawienia dokumentów księgowych.</p>'],
        ['title'=>'Cele przetwarzania','html'=>'<p>Dane przetwarzamy w celu obsługi zapytań, przygotowania wyceny, realizacji usług, prowadzenia korespondencji, zawarcia i wykonania umowy, rozliczeń, archiwizacji dokumentacji, obrony roszczeń oraz — jeśli wyrażono zgodę — działań marketingowych.</p>'],
        ['title'=>'Podstawy prawne','html'=>'<p>Podstawą przetwarzania danych może być podjęcie działań przed zawarciem umowy, wykonanie umowy, obowiązek prawny, prawnie uzasadniony interes administratora lub zgoda osoby, której dane dotyczą.</p>'],
        ['title'=>'Odbiorcy danych','html'=>'<p>Dane mogą być powierzane podmiotom wspierającym obsługę techniczną, hosting, pocztę e-mail, księgowość, analitykę, formularze, CRM, narzędzia reklamowe lub systemy potrzebne do realizacji projektu. Korzystamy wyłącznie z narzędzi niezbędnych do prowadzenia działalności i obsługi usług.</p>'],
        ['title'=>'Okres przechowywania','html'=>'<p>Dane przechowujemy przez czas niezbędny do obsługi zapytania, realizacji współpracy, wykonania obowiązków prawnych, przedawnienia roszczeń lub do czasu cofnięcia zgody — jeżeli przetwarzanie odbywa się na podstawie zgody.</p>'],
        ['title'=>'Prawa użytkownika','html'=>'<p>Przysługuje Ci prawo dostępu do danych, ich sprostowania, usunięcia, ograniczenia przetwarzania, przenoszenia danych, wniesienia sprzeciwu, cofnięcia zgody oraz wniesienia skargi do Prezesa UODO.</p><p>Żądania związane z realizacją praw można przesyłać mailowo. W niektórych przypadkach możemy poprosić o dodatkowe informacje potrzebne do potwierdzenia tożsamości osoby składającej żądanie lub doprecyzowania, którego procesu dotyczy zgłoszenie.</p>'],
        ['title'=>'Dobrowolność podania danych','html'=>'<p>Podanie danych jest dobrowolne, ale może być konieczne do przygotowania wyceny, odpowiedzi na wiadomość, zawarcia umowy, wystawienia faktury lub wykonania usługi. Bez części danych nie będziemy mogli skutecznie obsłużyć zapytania albo zrealizować projektu.</p>'],
        ['title'=>'Dane techniczne i analityka','html'=>'<p>Podczas korzystania ze strony mogą być przetwarzane dane techniczne, takie jak adres IP, informacje o urządzeniu, przeglądarce, źródle wejścia, czasie wizyty i sposobie korzystania ze strony. Dane te pomagają utrzymać bezpieczeństwo, diagnozować błędy, analizować skuteczność treści oraz rozwijać stronę.</p>'],
        ['title'=>'Bezpieczeństwo danych','html'=>'<p>Stosujemy organizacyjne i techniczne środki bezpieczeństwa adekwatne do charakteru przetwarzanych danych, w tym ograniczenie dostępu, zabezpieczenia kont, narzędzia hostingowe, kopie zapasowe oraz bieżącą aktualizację środowiska strony, jeżeli pozostaje pod naszą opieką techniczną.</p>'],
      ],
    ],
    'rodo' => [
      'title' => 'RODO',
      'lead' => 'Najważniejsze informacje dotyczące przetwarzania danych osobowych zgodnie z RODO w ramach kontaktu i współpracy z AP Studio / Zaprojektowani.com.',
      'sections' => [
        ['title'=>'Kto jest administratorem?','html'=>'<p>Administratorem danych jest <strong>AP Studio</strong>, NIP: <strong>9930682613</strong>, ul. Modelarska 18/2, 40-142 Katowice. Kontakt: '.$mail.', tel. <a href="tel:+48501054253">+48 501 054 253</a>.</p>'],
        ['title'=>'Dlaczego przetwarzamy dane?','html'=>'<p>Dane są przetwarzane, aby odpowiedzieć na zapytanie, przygotować ofertę, prowadzić projekt, obsłużyć płatności, wystawić dokumenty, zapewnić kontakt po wdrożeniu oraz zabezpieczyć ewentualne roszczenia.</p>'],
        ['title'=>'Jakie dane mogą być przetwarzane?','html'=>'<p>Najczęściej są to: dane kontaktowe, dane firmy, informacje projektowe, treści briefu, przesłane pliki, historia korespondencji, dane rozliczeniowe oraz dane techniczne związane z korzystaniem ze strony.</p>'],
        ['title'=>'Komu możemy przekazać dane?','html'=>'<p>Dane mogą być przekazywane dostawcom hostingu, poczty, narzędzi formularzy, systemów analitycznych, księgowości, obsługi IT, prawnej oraz podmiotom, które pomagają nam realizować usługę. Dane nie są sprzedawane.</p>'],
        ['title'=>'Twoje prawa','html'=>'<p>Masz prawo dostępu do danych, poprawienia danych, usunięcia danych, ograniczenia przetwarzania, wniesienia sprzeciwu, przeniesienia danych, cofnięcia zgody oraz złożenia skargi do organu nadzorczego.</p><p>Jeżeli przetwarzanie odbywa się na podstawie zgody, możesz ją cofnąć w dowolnym momencie. Cofnięcie zgody nie wpływa na zgodność z prawem przetwarzania dokonanego przed jej cofnięciem.</p><div class="zpLegal__note"><p>W sprawach związanych z RODO napisz na: <strong>'.$d['email'].'</strong>.</p></div>'],
        ['title'=>'Poufność materiałów projektowych','html'=>'<p>Materiały przekazane do wyceny lub realizacji projektu, takie jak briefy, logotypy, dane dostępowe, pliki graficzne, opisy produktów, informacje o firmie lub dane klientów końcowych, traktujemy jako informacje projektowe i wykorzystujemy wyłącznie w zakresie potrzebnym do obsługi zlecenia.</p>'],
        ['title'=>'Powierzenie przetwarzania','html'=>'<p>Jeżeli w ramach realizacji projektu konieczne jest przetwarzanie danych klientów lub użytkowników końcowych klienta, zakres odpowiedzialności, role stron oraz ewentualna umowa powierzenia mogą zostać ustalone odrębnie, w zależności od charakteru projektu.</p>'],
      ],
    ],
    'cookies' => [
      'title' => 'Polityka cookies',
      'lead' => 'Informacje o plikach cookies i podobnych technologiach wykorzystywanych na stronie zaprojektowani.com.',
      'sections' => [
        ['title'=>'Czym są cookies?','html'=>'<p>Cookies to niewielkie pliki zapisywane na urządzeniu użytkownika. Mogą wspierać prawidłowe działanie strony, zapamiętywanie ustawień, bezpieczeństwo, analitykę oraz działania marketingowe.</p>'],
        ['title'=>'Jakie cookies możemy wykorzystywać?','html'=>'<p>Strona może korzystać z cookies technicznych, funkcjonalnych, analitycznych i marketingowych. Mogą one pochodzić od właściciela strony lub od zewnętrznych narzędzi, takich jak systemy analityczne, piksele reklamowe, mapy, formularze lub osadzone multimedia.</p>'],
        ['title'=>'Cele używania cookies','html'=>'<p>Cookies mogą być używane do utrzymania działania strony, poprawy szybkości i bezpieczeństwa, analizy ruchu, mierzenia skuteczności kampanii, zapamiętywania preferencji oraz lepszego dopasowania komunikacji reklamowej.</p>'],
        ['title'=>'Zarządzanie cookies','html'=>'<p>Użytkownik może zarządzać plikami cookies z poziomu ustawień swojej przeglądarki. Ograniczenie cookies może wpłynąć na część funkcji strony, formularzy, statystyk lub osadzonych narzędzi.</p>'],
        ['title'=>'Kontakt','html'=>'<p>W sprawach dotyczących cookies i prywatności można skontaktować się z nami mailowo: '.$mail.'.</p>'],
        ['title'=>'Cookies zewnętrznych dostawców','html'=>'<p>Na stronie mogą działać narzędzia zewnętrzne, np. analityka, formularze, mapy, piksele reklamowe, systemy osadzania treści lub narzędzia bezpieczeństwa. Dostawcy tych narzędzi mogą wykorzystywać własne cookies zgodnie ze swoimi politykami prywatności.</p>'],
        ['title'=>'Zmiana ustawień i zgody','html'=>'<p>Ustawienia cookies można zmienić w przeglądarce internetowej. W przypadku wdrożenia banera zgód lub centrum preferencji, użytkownik może również zarządzać zgodami z poziomu mechanizmu dostępnego na stronie.</p>'],
      ],
    ],
  ];
}

function zp_suite_legal_make_elementor_data($html){
  return [
    [
      'id' => substr(md5('zp-legal-section'), 0, 7),
      'elType' => 'section',
      'settings' => ['layout' => 'full_width', 'gap' => 'no'],
      'elements' => [[
        'id' => substr(md5('zp-legal-column'), 0, 7),
        'elType' => 'column',
        'settings' => ['_column_size' => 100],
        'elements' => [[
          'id' => substr(md5('zp-legal-html'), 0, 7),
          'elType' => 'widget',
          'widgetType' => 'html',
          'settings' => ['html' => $html],
          'elements' => [],
        ]],
      ]],
    ],
  ];
}

function zp_suite_legal_create_or_update_pages(){
  if (!function_exists('wp_insert_post')) { return; }
  $defs = zp_suite_legal_pages_definitions();
  foreach ($defs as $slug => $def) {
    $html = zp_suite_legal_shell($def['title'], $def['lead'], $def['sections']);
    $page = get_page_by_path($slug, OBJECT, 'page');
    $postarr = [
      'post_title' => $def['title'],
      'post_name' => $slug,
      'post_content' => $html,
      'post_status' => 'publish',
      'post_type' => 'page',
      'comment_status' => 'closed',
      'ping_status' => 'closed',
    ];
    if ($page && !empty($page->ID)) {
      $postarr['ID'] = (int) $page->ID;
      $post_id = wp_update_post(wp_slash($postarr), true);
    } else {
      $post_id = wp_insert_post(wp_slash($postarr), true);
    }
    if (is_wp_error($post_id) || !$post_id) { continue; }
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    update_post_meta($post_id, '_elementor_template_type', 'wp-page');
    update_post_meta($post_id, '_elementor_version', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.0.0');
    update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode(zp_suite_legal_make_elementor_data($html), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));
  }
  update_option('zp_suite_legal_pages_version', '2.1.18', false);
}

function zp_suite_legal_admin_maybe_create_pages(){
  if (!is_admin() || wp_doing_ajax() || (defined('WP_CLI') && WP_CLI)) { return; }
  if (get_option('zp_suite_legal_pages_version') !== '2.1.18') {
    zp_suite_legal_create_or_update_pages();
  }
}
add_action('admin_init', 'zp_suite_legal_admin_maybe_create_pages', 20);
