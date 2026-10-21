<?php
/** Editorial additions; selected URLs only. All illustrations already ship in the suite. */
if (!defined('ABSPATH')) { exit; }

return [
    'date' => '2026-10-17',
    'images' => [
        'siemianowski-web' => [
            'file' => 'strona-kancelarii-laptop.webp', 'width' => 1344, 'height' => 756,
            'alt' => 'Wizualizacja strony Kancelarii Siemianowski z opisem specjalizacji i przyciskiem umówienia wizyty',
            'caption' => 'Kancelaria Siemianowski — wizualizacja projektu Zaprojektowani. Pierwszy ekran przedstawia specjalizację, a dalsza nawigacja rozdziela zakresy pomocy.',
        ],
        'krawiec-web' => [
            'file' => 'strona-krawca-z-umawianiem-wizyt.webp', 'width' => 1344, 'height' => 756,
            'alt' => 'Projekt strony Krawiec z dojazdem na laptopie oraz mobilny formularz kontaktowy',
            'caption' => 'Krawiec z dojazdem — wizualizacja projektu Zaprojektowani. Na komputerze widoczna jest oferta szycia na miarę, na telefonie formularz kontaktu.',
        ],
        'siemianowski-brand' => [
            'file' => 'identyfikacja-kancelarii-plansza.webp', 'width' => 1344, 'height' => 756,
            'alt' => 'Identyfikacja Kancelarii Siemianowski: znak na jasnym i granatowym tle oraz wizualizacje materiałów firmowych',
            'caption' => 'Kancelaria Siemianowski — warianty znaku i wizualizacje materiałów z portfolio Zaprojektowani. Różne nośniki łączy ta sama konstrukcja logo i paleta.',
        ],
        'vista-brand' => [
            'file' => 'znak-pracowni-architektury.webp', 'width' => 1344, 'height' => 756,
            'alt' => 'Logo Vista Architekci: pełna nazwa, sam geometryczny sygnet, kontury i wersja biała na ciemnym tle',
            'caption' => 'Vista Architekci — plansza projektu Zaprojektowani: pełne logo, sam sygnet, rysunek konturowy i wersja na ciemne tło.',
        ],
        'apartament-brand' => [
            'file' => 'logo-i-strona-salonu-apartament-piekna.webp', 'width' => 1344, 'height' => 756,
            'alt' => 'Wizualizacja identyfikacji Apartamentu Piękna na stronie internetowej, kartach wizyt i opakowaniach',
            'caption' => 'Apartament Piękna — wizualizacja zastosowań marki z portfolio Zaprojektowani: strona, widok rezerwacji i materiały towarzyszące. Makieta przedstawia kierunek wizualny poszczególnych nośników.',
        ],
        'apartament-web' => [
            'file' => 'strona-docelowa-apartament-piekna.webp', 'width' => 1344, 'height' => 756,
            'alt' => 'Strona Apartamentu Piękna na laptopie i widok ścieżki umówienia konsultacji na telefonie',
            'caption' => 'Apartament Piękna — wizualizacja strony firmowej i ścieżki konsultacji. To przykład sposobu prowadzenia do rezerwacji, który można analizować przy planowaniu landing page.',
        ],
        'wonder-brand' => [
            'file' => 'wersje-znaku-marki.webp', 'width' => 1344, 'height' => 756,
            'alt' => 'Logo Wonder Handmade w czerni, bieli i złotym kolorze oraz wizualizacja użycia na materiałach',
            'caption' => 'Wonder Handmade — projekt Zaprojektowani w wersji jednobarwnej, w negatywie i w kolorze marki. Plansza pokazuje warianty użycia, a pliki produkcyjne dobiera się osobno do techniki wykonania.',
        ],
    ],
    'articles' => [
        '/strony-internetowe/jak-stworzyc-strone-internetowa/' => [
            'text_fixes' => [
                ['Hosting, szablony i edytor w jednym abonamencie. Najszybszy start, ale strona zostaje na platformie i nie przeniesiesz jej w całości gdzie indziej.',
                 'Hosting, szablony i edytor w jednym abonamencie. Możliwości przeniesienia strony zależą od platformy i zakresu funkcji; przed wyborem sprawdź eksport kodu, treści oraz obsługę formularzy po migracji.'],
            ],
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-strona-przyklady', 'after' => 'agencja',
                    'toc' => 'Dwa przykłady stron firmowych', 'label' => 'Przykłady z portfolio',
                    'title' => 'Jak cel firmy zmienia projekt strony? Dwa konkretne przykłady',
                    'html' => <<<'HTML'
<p>Łatwiej wybrać sposób stworzenia strony, gdy zapiszesz jedną rzeczywistą drogę klienta: od pytania, z którym przychodzi, do kontaktu z firmą. Poniższe projekty pokazują, dlaczego podobny zestaw narzędzi może prowadzić do innej struktury. Własny brief oprzyj na tym, co odbiorca powinien znaleźć i zrobić, zanim poprosisz o wybór motywu albo wycenę wdrożenia.</p>
<h3>Kancelaria Siemianowski: kilka potrzeb, czytelny podział oferty</h3>
<p>Na stronie kancelarii użytkownik musi najpierw rozpoznać, czy firma zajmuje się jego sprawą. Na prezentowanym projekcie nagłówek określa specjalizację, a nawigacja i opisy kierują do właściwego zakresu pomocy. Taka strona potrzebuje przemyślanych podstron usług, informacji o osobie prowadzącej sprawę oraz drogi do konsultacji. Sam duży napis „Witamy na naszej stronie” nie wykona tych zadań.</p>
<p><strong>Co wpisać do własnego briefu:</strong> najważniejsze grupy klientów, zakres każdej usługi, pytania przed kontaktem i materiały, które potwierdzają kompetencje. W <a href="{{case:siemianowski}}">opisie realizacji Kancelarii Siemianowski</a> pokazujemy, jak połączono stronę z dodatkowymi funkcjami potrzebnymi w działalności kancelarii.</p>
{{image:siemianowski-web}}
<h3>Krawiec z dojazdem: oferta prowadząca do umówienia kontaktu</h3>
<p>W tym projekcie już pierwszy ekran mówi o szyciu na miarę i dojeździe. Przycisk zaprasza do umówienia wizyty, a na mobilnym widoku pokazujemy formularz. Dla podobnej firmy ważne są obszar obsługi, sposób pomiaru, przykłady wykonania i informacja, co dzieje się po wysłaniu zapytania. Nie każda usługa wymaga od razu sklepu, konta klienta czy płatności online.</p>
<p><strong>Ćwiczenie przed projektem:</strong> zapisz zdanie „Klient szuka…, powinien zobaczyć…, a następnie…”. Jeśli odpowiedzią są trzy różne działania, wybierz najważniejsze na start. Dopiero potem ustal, czy wystarczy formularz, czy potrzebujesz systemu rezerwacji. Pomaga w tym porównanie <a href="{{link:/strony-internetowe/rodzaje-stron-internetowych/}}">rodzajów stron internetowych</a>. W ramach <a href="{{link:/tworzenie-stron-internetowych/}}">tworzenia strony dla firmy</a> taki scenariusz można przełożyć na strukturę, treści i projekt mobilny.</p>
{{image:krawiec-web}}
HTML,
                ],
                [
                    'id' => 'zp-growth-20261017-strona-odbior', 'after' => 'co-musi-miec',
                    'toc' => 'Siedem prób przed publikacją', 'label' => 'Odbiór strony',
                    'title' => 'Siedem prób, które warto wykonać przed publikacją strony firmowej',
                    'html' => <<<'HTML'
<p>Odbieraj stronę, wykonując zadania klienta i osoby, która będzie ją obsługiwać. Sam podgląd strony głównej nie sprawdzi dostarczania wiadomości, poprawności telefonu ani tego, czy zespół potrafi edytować ofertę. Dla każdej próby zapisz wynik i osobę odpowiedzialną za ewentualną poprawkę.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Próby odbioru strony firmowej" tabindex="0"><table><thead><tr><th scope="col">Próba</th><th scope="col">Co zrobić</th><th scope="col">Co potwierdza działanie</th></tr></thead><tbody>
<tr><th scope="row">Oferta</th><td>Wejdź bezpośrednio na podstronę usługi z telefonu.</td><td>Wiadomo, dla kogo jest usługa, co obejmuje i jak zapytać o szczegóły.</td></tr>
<tr><th scope="row">Formularz</th><td>Wyślij prawidłowe zapytanie oraz spróbuj wysłać puste wymagane pola.</td><td>Wiadomość dociera do właściwej skrzynki, a błędy są zrozumiałe.</td></tr>
<tr><th scope="row">Telefon</th><td>Dotknij numeru i przycisku kontaktu.</td><td>Telefon proponuje połączenie z właściwym numerem.</td></tr>
<tr><th scope="row">Mobile</th><td>Otwórz menu, przejdź do oferty i wypełnij formularz z klawiaturą ekranową.</td><td>Da się wykonać całą ścieżkę bez przesłoniętych pól i przewijania w bok.</td></tr>
<tr><th scope="row">Edycja</th><td>Zmień przykładowy tekst i zdjęcie, następnie cofnij zmianę.</td><td>Osoba z firmy potrafi wykonać te czynności na swoim koncie.</td></tr>
<tr><th scope="row">Dostępy</th><td>Sprawdź konta domeny, hostingu i panelu strony.</td><td>Wiadomo, kto ma dostęp, kto odnawia usługi i do kogo zgłasza się awarię.</td></tr>
<tr><th scope="row">Widoczność</th><td>Sprawdź docelowy adres w Search Console oraz ustawienia indeksowania.</td><td>Publiczna strona nie ma przypadkowej blokady po etapie roboczym.</td></tr>
</tbody></table></div>
<p>W WordPressie opcja zniechęcania wyszukiwarek do indeksowania wymaga osobnej kontroli przy starcie. Jej znaczenie opisuje <a href="https://wordpress.org/documentation/article/settings-reading-screen/" rel="noopener" target="_blank">dokumentacja ustawień czytania WordPressa</a>. <a href="https://support.google.com/webmasters/answer/9012289" rel="noopener" target="_blank">Test aktywnego adresu URL w Search Console</a> pomaga sprawdzić dostępność i wyrenderowaną stronę; pozytywny test nie oznacza automatycznego pojawienia się w wynikach.</p>
<p>Po starcie prowadź prosty rejestr zapytań: jakiej usługi dotyczyły, skąd przyszły i czy mieściły się w ofercie. To daje konkretną podstawę do rozwoju treści. Jeśli największą luką są opisy oferty, przejdź do poradnika <a href="{{link:/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/}}">co powinna zawierać strona internetowa dla firmy</a>.</p>
HTML,
                ],
            ],
        ],
        '/logo-branding/identyfikacja-wizualna-firmy/' => [
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-identyfikacja-przyklady', 'after' => 'elementy',
                    'toc' => 'Identyfikacja na dwóch przykładach', 'label' => 'Przykłady z portfolio',
                    'title' => 'Identyfikacja wizualna w praktyce: kancelaria i pracownia architektury',
                    'html' => <<<'HTML'
<p>System identyfikacji najłatwiej ocenić na kilku rzeczywistych zastosowaniach pokazanych obok siebie. Przy każdym sprawdź, co pozostaje stałe, a co zmienia się wraz z nośnikiem. Logo może występować w różnych wielkościach, ale nazwa, proporcje, paleta i charakter typografii powinny tworzyć rozpoznawalną całość.</p>
<h3>Kancelaria Siemianowski: jeden znak, kilka warunków użycia</h3>
<p>Plansza pokazuje logo na jasnym i granatowym tle oraz wizualizacje materiałów firmowych. Wspólne elementy to konstrukcja sygnetu, sposób zapisu nazwy i paleta. Na ciemnym tle potrzebny jest wariant zachowujący czytelność, a przy tłoczeniu lub innym uszlachetnieniu trzeba przygotować plik odpowiadający technice wykonania. Efekt na makiecie pomaga omówić kierunek; końcowy detal należy ocenić na próbce od producenta.</p>
<p><strong>Wniosek dla własnej firmy:</strong> poproś o prezentację na nośnikach, których faktycznie używasz. Jeśli większość kontaktu z klientem odbywa się przez ofertę PDF i stronę, te dwa zastosowania powinny znaleźć się w ocenie projektu już na początku.</p>
{{image:siemianowski-brand}}
<h3>Vista Architekci: pełna nazwa i znak do mniejszych formatów</h3>
<p>W przykładzie Vista Architekci obok pełnego układu występuje sam geometryczny sygnet oraz wersja na ciemne tło. To dobry punkt wyjścia do ustalenia, gdzie potrzebna jest pełna nazwa, a gdzie wystarczy mały znak. Innych proporcji wymaga okładka dokumentacji, innych ikona profilu czy nagłówek strony. Decyzję o uproszczeniu warto zapisać w instrukcji, aby każdy wykonawca nie kadrował logo na własny sposób.</p>
<p>Spójność nie wymaga kopiowania jednego układu na wszystkie materiały. W ofercie miejsce zajmie opis usługi, na wizytówce kontakt, a na stronie przycisk i nawigacja. System powinien pomagać zachować rodzinę kolorów, kroje pisma i sposób wykorzystania znaku przy różnych ilościach informacji. Jak to uporządkować, pokazujemy również w poradniku o <a href="{{link:/logo-branding/ksiega-znaku-co-to-jest-co-zawiera-i-kiedy-firma-jej-potrzebuje/}}">księdze znaku i zasadach użycia logo</a>.</p>
{{image:vista-brand}}
HTML,
                ],
                [
                    'id' => 'zp-growth-20261017-identyfikacja-odbior', 'after' => 'wdrozenie',
                    'toc' => 'Test odbioru identyfikacji', 'label' => 'Do wykorzystania w firmie',
                    'title' => 'Jak odebrać identyfikację, z której zespół potrafi korzystać?',
                    'html' => <<<'HTML'
<p>Przy odbiorze projektu sprawdź, czy osoba przygotowująca ofertę, post albo zlecenie do drukarni potrafi samodzielnie znaleźć właściwy materiał. Liczba plików w paczce nie mówi jeszcze, czy system jest gotowy do pracy. Przydatniejsza będzie krótka próba na czterech codziennych zadaniach.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Test odbioru identyfikacji wizualnej firmy" tabindex="0"><table><thead><tr><th scope="col">Zadanie</th><th scope="col">Co sprawdzić</th><th scope="col">Co powinno być dostępne</th></tr></thead><tbody>
<tr><th scope="row">Oferta dla klienta</th><td>Czy zmiana nazwy usługi i liczby stron nie psuje kompozycji?</td><td>Edytowalny szablon, style tekstu i przykład wypełnienia.</td></tr>
<tr><th scope="row">Grafika do social mediów</th><td>Czy dłuższy nagłówek oraz pionowe zdjęcie nadal mieszczą się w układzie?</td><td>Warianty formatów i reguły stosowania zdjęć, koloru oraz logo.</td></tr>
<tr><th scope="row">Zlecenie dla drukarni</th><td>Czy wykonawca dostaje właściwy wariant bez odtwarzania znaku?</td><td>Pliki do ustalonej techniki, kody kolorów i kontakt do osoby zatwierdzającej próbkę.</td></tr>
<tr><th scope="row">Aktualizacja strony</th><td>Czy znak jest czytelny w nagłówku, stopce i małej ikonie?</td><td>Eksporty ekranowe, dopuszczalne tła i zasady minimalnego rozmiaru.</td></tr>
</tbody></table></div>
<p>Ustal jedno miejsce przechowywania zatwierdzonych materiałów i osobę odpowiedzialną za ich aktualność. Stare koncepcje zachowaj w osobnym archiwum. Nowy pracownik powinien od razu wiedzieć, gdzie znajduje się wersja do użycia, a gdzie dokumentacja projektu. Przed przekazaniem szablonów sprawdź także, czy firma ma potrzebne dostępy do narzędzi oraz licencje na wybrane kroje pisma.</p>
<p>W <a href="{{link:/identyfikacja-wizualna/}}">projekcie identyfikacji wizualnej</a> warto uzgodnić te kryteria już w zakresie prac. Specyfikację formatów omawiamy w tekście <a href="{{link:/logo-branding/logo-wektorowe-pliki-logo/}}">logo wektorowe i pliki dla firmy</a>. Jeśli obecne materiały są niespójne mimo dobrego logo, zacznij od uporządkowania zasad użycia; szerszą zmianę opisuje poradnik o <a href="{{link:/logo-branding/rebranding-firmy/}}">rebrandingu firmy</a>.</p>
HTML,
                ],
            ],
        ],
        '/logo-branding/rebranding-firmy/' => [
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-rebranding-zastosowania', 'after' => 'roznice',
                    'toc' => 'Zakres zmiany na przykładach', 'label' => 'Od znaku do zastosowań',
                    'title' => 'Jak określić zakres rebrandingu? Dwa przykłady zastosowań marki',
                    'html' => <<<'HTML'
<p>Przed wyceną rebrandingu zbierz materiały, z którymi klient styka się podczas zakupu lub kontaktu. Lista powinna obejmować stronę, profile, dokumenty, oznaczenia w lokalu i elementy używane przez zespół. Wizualizacje poniższych projektów pomagają zobaczyć, jak wiele miejsc może łączyć jedna identyfikacja. Służą jako przykłady zakresu wdrożenia gotowej marki.</p>
<h3>Usługa mobilna: oferta, kontakt i materiały w jednym języku wizualnym</h3>
<p>W projekcie Krawiec z dojazdem nazwa, ciemna paleta oraz akcenty łączą stronę internetową z materiałami towarzyszącymi. Gdy podobna firma zmienia markę, warto zacząć od miejsc, w których klient wybiera usługę i umawia kontakt: strony, profilu, formularza, odpowiedzi e-mail i wizytówki. Taka lista jest konkretniejsza niż zamówienie „nowego logo i kilku grafik”.</p>
<p>Przy każdym materiale dopisz właściciela, format i termin wymiany. Sprawdź, które elementy można zaktualizować od razu, a które zależą od produkcji albo zapasu wydruków. Jeśli nazwa pozostaje ta sama, ustal także, jakie cechy obecnego znaku warto zachować, by dotychczasowi odbiorcy rozpoznali firmę.</p>
{{image:krawiec-web}}
<h3>Marka z wizytami: zgodność strony i doświadczenia na miejscu</h3>
<p>Wizualizacja Apartamentu Piękna zestawia stronę, widok umówienia wizyty i materiały towarzyszące. Przy planowaniu podobnej zmiany przejdź cały scenariusz klienta: wejście na stronę, wybór usługi, kontakt, potwierdzenie i wizyta. W każdym miejscu sprawdź nazwę, znak, kolorystykę oraz aktualne dane. Zespół powinien wcześniej otrzymać gotowe pliki i wiedzieć, od kiedy zaczynają obowiązywać.</p>
<p>Nie trzeba wymieniać wszystkich nośników w tej samej godzinie. Trzeba natomiast zaplanować okres przejściowy, żeby odbiorca potrafił połączyć stare i nowe materiały z jedną firmą. W <a href="{{case:apartament}}">opisie projektu Apartamentu Piękna</a> można zobaczyć szerszy zakres połączenia identyfikacji i strony. Zasady utrzymania systemu opisujemy w poradniku <a href="{{link:/logo-branding/identyfikacja-wizualna-firmy/}}">identyfikacja wizualna firmy</a>.</p>
{{image:apartament-brand}}
HTML,
                ],
                [
                    'id' => 'zp-growth-20261017-rebranding-premiera', 'after' => 'proces',
                    'toc' => 'Plan premiery i kontrola zmiany', 'label' => 'Praktyczny plan wdrożenia',
                    'title' => 'Premiera rebrandingu: kolejność, odpowiedzialność i testy',
                    'html' => <<<'HTML'
<p>Gotowe logo to początek etapu wdrożenia. Przed premierą ułóż jedną tabelę materiałów i wpisz osoby odpowiedzialne za ich zmianę. Każda pozycja powinna mieć warunek odbioru, ponieważ sam status „wysłane do wykonawcy” nie potwierdza, że klient zobaczy właściwą wersję.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Plan wdrożenia rebrandingu firmy" tabindex="0"><table><thead><tr><th scope="col">Etap</th><th scope="col">Odpowiedzialność</th><th scope="col">Warunek zakończenia</th></tr></thead><tbody>
<tr><th scope="row">Paczka główna</th><td>Projektant i osoba zatwierdzająca w firmie.</td><td>Zatwierdzone warianty, instrukcja użycia oraz oddzielone archiwum starej wersji.</td></tr>
<tr><th scope="row">Przygotowanie zespołu</th><td>Osoba koordynująca wdrożenie.</td><td>Zespół zna termin zmiany, nowe szablony i sposób wyjaśniania jej klientom.</td></tr>
<tr><th scope="row">Kanały cyfrowe</th><td>Opiekun strony i profili.</td><td>Sprawdzone logo, nazwa, dane, podglądy udostępnień i wiadomości z formularzy.</td></tr>
<tr><th scope="row">Druk i oznakowanie</th><td>Osoba zamawiająca materiały i producent.</td><td>Zaakceptowana próbka, termin wymiany oraz decyzja, co zrobić ze starym nakładem.</td></tr>
<tr><th scope="row">Kontrola po premierze</th><td>Koordynator i osoby obsługujące klientów.</td><td>Sprawdzona ścieżka od wejścia na stronę do kontaktu i lista pozostałych starych materiałów.</td></tr>
</tbody></table></div>
<p>Jeżeli rebranding obejmuje zmianę domeny lub adresów podstron, przygotuj osobną mapę stary adres → odpowiadająca mu nowa strona. Uporządkuj przekierowania, linki wewnętrzne, adresy kanoniczne i mapę witryny. Te czynności oraz możliwe wahania podczas przenosin opisuje <a href="https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes" rel="noopener" target="_blank">dokumentacja Google dotycząca zmiany adresów witryny</a>. Zmiana samego wyglądu marki nie wymaga przy okazji przebudowy wszystkich adresów.</p>
<p>Po premierze zbieraj konkretne uwagi: czy klienci rozpoznają firmę, czy zespół znajduje właściwe pliki i gdzie nadal wraca stary znak. Porównując zapytania, uwzględnij równoczesne zmiany kampanii, oferty i sezonu. Dzięki temu nie przypiszesz każdej różnicy samemu logo. Zakres prac można uporządkować przed rozpoczęciem <a href="{{link:/identyfikacja-wizualna/}}">projektu nowej identyfikacji wizualnej</a>, a kwestie ciągłości rozwija poradnik o <a href="{{link:/logo-branding/rebranding-bez-utraty-rozpoznawalnosci-marki/}}">zmianie marki z zachowaniem rozpoznawalności</a>.</p>
HTML,
                ],
            ],
        ],
        '/logo-branding/logo-wektorowe-pliki-logo/' => [
            'text_fixes' => [
                ['Do podglądów i miejsc, które nie przyjmują PNG, ale nie do druku.',
                 'Do podglądów i miejsc, które wymagają JPG. Przy druku logo wektor jest zwykle lepszym materiałem; użycie bitmapy zależy od rozmiaru i wymagań wykonawcy.'],
            ],
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-wektor-warianty', 'after' => 'jaki-plik',
                    'toc' => 'Dwa przykłady wersji logo', 'label' => 'Przykłady z portfolio',
                    'title' => 'Wektor to format zapisu. Jakie wersje znaku przygotować do użycia?',
                    'html' => <<<'HTML'
<p>Jeden poprawny plik wektorowy nie zastępuje decyzji o wariantach logo. Firma może potrzebować pełnej nazwy na stronie, samego sygnetu w małej ikonie i wersji jednobarwnej do konkretnej techniki produkcji. Każdy plik powinien odtwarzać zatwierdzoną wersję znaku. Poniższe plansze pokazują, co warto rozróżnić przy odbiorze projektu.</p>
<h3>Vista Architekci: pełny układ, sygnet i negatyw</h3>
<p>Na planszy Vista obok pełnej nazwy znajduje się sam znak, rysunek konturowy i biała wersja na ciemnym tle. Przy przekazaniu plików ustal, które układy są przeznaczone do użycia, a które wyjaśniają konstrukcję. Widok konturów na planszy prezentacyjnej nie oznacza jeszcze, że odbiorca dostał produkcyjny plik z prawidłowymi ścieżkami.</p>
<p>Praktyczny test polega na otwarciu finalnego pliku w programie wektorowym i zaznaczeniu kształtów. Sprawdź także drobny dopisek w rzeczywistym rozmiarze. Może być czytelny na dużej planszy, a zbyt mały na ikonie. Zamiast samodzielnie usuwać litery, użyj przygotowanego wariantu uproszczonego.</p>
{{image:vista-brand}}
<h3>Wonder Handmade: kolor, biel i wersja jednobarwna</h3>
<p>W przypadku Wonder te same kształty pokazano w czerni, bieli i kolorze marki. Na wizualizacji materiałów widać też efekty wykończenia. Plik do tłoczenia lub folii powinien odpowiadać instrukcji wykonawcy; zdjęcie efektu z prezentacji służy jako odniesienie wizualne. Samo zapisanie złotego koloru ekranowego nie opisuje rodzaju folii, podłoża ani sposobu produkcji.</p>
<p>Poproś o jednoznaczne nazwy wersji i zapis zastosowań w <a href="{{link:/logo-branding/ksiega-znaku-co-to-jest-co-zawiera-i-kiedy-firma-jej-potrzebuje/}}">instrukcji logo lub księdze znaku</a>. Wtedy osoba z firmy może przekazać drukarni właściwy wariant bez wybierania „tego, który ładniej wygląda w podglądzie”. Podstawy zapisu ścieżek, fontów i obrazów osadzonych opisuje <a href="https://helpx.adobe.com/illustrator/using/saving-artwork.html" rel="noopener" target="_blank">dokumentacja zapisywania plików w Illustratorze</a>.</p>
{{image:wonder-brand}}
HTML,
                ],
                [
                    'id' => 'zp-growth-20261017-wektor-paczka', 'after' => 'komplet',
                    'toc' => 'Przykład uporządkowanej paczki', 'label' => 'Przekazanie plików',
                    'title' => 'Przykład nazw plików logo i testów przed wysłaniem ich wykonawcy',
                    'html' => <<<'HTML'
<p>Nazwa pliku powinna mówić, co jest w środku i gdzie można go użyć. Przy kilku wariantach łatwo pomylić białe logo z pustym obrazem albo wysłać do drukarni mały podgląd. Poniższy układ to przykład organizacji paczki; zakres eksportów zawsze dopasuj do projektu i uzgodnionych zastosowań.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Przykładowe nazwy plików logo i ich odbiorcy" tabindex="0"><table><thead><tr><th scope="col">Przykładowa nazwa</th><th scope="col">Dla kogo</th><th scope="col">Test przed przekazaniem</th></tr></thead><tbody>
<tr><th scope="row"><code>marka-logo-rgb-kolor.svg</code></th><td>Osoba wdrażająca stronę.</td><td>Czy kształty są ostre, proporcje prawidłowe, a plik nie wymaga brakujących zasobów?</td></tr>
<tr><th scope="row"><code>marka-logo-cmyk.pdf</code></th><td>Drukarnia, zgodnie z jej specyfikacją.</td><td>Czy znak ma właściwy zapis, wariant barwny i litery przygotowane do produkcji?</td></tr>
<tr><th scope="row"><code>marka-logo-bialy.png</code></th><td>Osoba składająca dokument lub grafikę.</td><td>Czy białe logo jest widoczne po umieszczeniu na docelowym ciemnym tle?</td></tr>
<tr><th scope="row"><code>marka-sygnet-avatar.png</code></th><td>Osoba prowadząca profil firmy.</td><td>Czy po okrągłym kadrowaniu platformy znak pozostaje czytelny i ma margines?</td></tr>
<tr><th scope="row"><code>marka-logo-zrodlo.ai</code></th><td>Projektant rozwijający identyfikację.</td><td>Czy otwierają się potrzebne warstwy, a zależności i zakres przekazania są opisane?</td></tr>
</tbody></table></div>
<p>Dołącz krótką instrukcję ze zdjęciem podglądowym każdej wersji i numerem aktualnego wydania. Przechowuj osobno oryginały, gotowe eksporty oraz pliki archiwalne. Przy zmianie znaku wycofaj stare eksporty z folderu przeznaczonego dla wykonawców, żeby nowa wizytówka i nowy szyld nie powstały z różnych wersji.</p>
<p>Jeżeli plik SVG zawiera element <code>image</code>, może korzystać z osadzonego lub podłączonego obrazu — opisuje to <a href="https://developer.mozilla.org/en-US/docs/Web/SVG/Reference/Element/image" rel="noopener" target="_blank">dokumentacja SVG</a>. Sama końcówka nazwy nie potwierdza, że cały znak składa się ze ścieżek. Przy <a href="{{link:/projektowanie-logo/}}">zamawianiu projektu logo</a> ustal więc także sposób odbioru plików, a nie tylko listę rozszerzeń. W szerszym systemie marki paczka powinna współgrać z zasadami <a href="{{link:/logo-branding/identyfikacja-wizualna-firmy/}}">identyfikacji wizualnej firmy</a>.</p>
HTML,
                ],
            ],
        ],
        '/seo-i-konwersja/landing-page-przyklady/' => [
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-landing-realizacje', 'after' => 'cta-formularze',
                    'toc' => 'Wzorce z rzeczywistych projektów', 'label' => 'Od przykładów do realizacji',
                    'title' => 'Dwie strony firmowe, z których można wyciągnąć wnioski dla landing page',
                    'html' => <<<'HTML'
<p>Po modelowych przykładach warto przeanalizować rzeczywiste projekty. Strony Apartamentu Piękna i Kancelarii Siemianowski mają szerszy zakres niż pojedynczy landing kampanii, ale pokazują użyteczne wzorce: jak nazwać ofertę, uporządkować wybór i doprowadzić do kontaktu. Poniższa ocena dotyczy widocznej konstrukcji ścieżki; skuteczność własnej wersji trzeba później sprawdzić na zapytaniach.</p>
<h3>Apartament Piękna: od zainteresowania usługą do umówienia wizyty</h3>
<p>Na wizualizacji komputer przedstawia ofertę i przycisk umówienia wizyty, a telefon pokazuje kolejny widok związany z konsultacją. Przy planowaniu landingu dla podobnej usługi ustal, czy odbiorca zna już usługę, czy najpierw potrzebuje rozmowy. Od tego zależy główny komunikat i to, czy przycisk prowadzi do konkretnej rezerwacji, wyboru terminu czy formularza konsultacji.</p>
<p><strong>Co przenieść do briefu:</strong> obietnicę z reklamy, opis następnego kroku i odpowiedź na obawę przed kontaktem. Jeśli reklama dotyczy jednej konsultacji, odwiedzający powinien łatwo rozpoznać ją także na stronie. Nie zmuszaj go do ponownego przeszukiwania całej listy usług. Więcej kontekstu zawiera <a href="{{case:apartament}}">opis strony i rezerwacji Apartamentu Piękna</a>.</p>
{{image:apartament-web}}
<h3>Kancelaria Siemianowski: jasno określony zakres pomocy</h3>
<p>Pierwszy ekran wskazuje specjalizację, a układ strony porządkuje obszary działalności. Dla landingu kierowanego do jednej grupy odbiorców przydatna jest sama zasada dopasowania: opisz konkretny problem, zakres wsparcia i sposób rozpoczęcia rozmowy. Osoba przechodząca z reklamy konkretnej usługi powinna szybko znaleźć właśnie tę usługę i warunki kontaktu.</p>
<p>Na potrzeby własnego projektu przygotuj krótki opis: „Skąd przychodzi użytkownik? Jaką ofertę widział? Co musi wyjaśnić strona? Dokąd prowadzi przycisk?”. Przejdź ścieżkę również z telefonu. Projekt firmowy może mieć rozbudowane menu, natomiast zakres nawigacji w landingu ustal pod jego zadanie. <a href="{{case:siemianowski}}">Realizacja Kancelarii Siemianowski</a> daje punkt odniesienia do architektury usług; sam landing należy dopasować do konkretnej kampanii.</p>
{{image:siemianowski-web}}
HTML,
                ],
                [
                    'id' => 'zp-growth-20261017-landing-test', 'after' => 'bez-kopiowania',
                    'toc' => 'Plan testu własnego landing page', 'label' => 'Sprawdzenie po uruchomieniu',
                    'title' => 'Jak sprawdzić, czy wybrany wzorzec działa dla Twojej oferty?',
                    'html' => <<<'HTML'
<p>Wybierz problem, który chcesz rozwiązać, zanim porównasz dwie wersje strony. Przykładowo: odwiedzający nie rozumieją zakresu konsultacji, formularz wymaga zbyt wielu informacji albo po kliknięciu przycisku nie wiedzą, co zrobić dalej. Z takiej obserwacji powstaje konkretna zmiana i kryterium oceny.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Plan oceny zmian na landing page" tabindex="0"><table><thead><tr><th scope="col">Obserwacja</th><th scope="col">Zmiana do sprawdzenia</th><th scope="col">Co obserwować</th></tr></thead><tbody>
<tr><th scope="row">Niejasny zakres oferty</th><td>Opisz rezultat kontaktu i to, co obejmuje usługa, blisko głównego przycisku.</td><td>Czy zapytania lepiej odpowiadają ofercie i czy wracają te same pytania?</td></tr>
<tr><th scope="row">Zatrzymanie przy formularzu</th><td>Zostaw pola potrzebne do pierwszej odpowiedzi i wyjaśnij cel nietypowych pytań.</td><td>Rozpoczęcia, błędy i poprawnie dostarczone wiadomości.</td></tr>
<tr><th scope="row">Rozbieżność reklamy i strony</th><td>Dopasuj nagłówek oraz ofertę do konkretnego komunikatu reklamy.</td><td>Jakość zapytań osobno dla danego źródła ruchu.</td></tr>
<tr><th scope="row">Trudność na telefonie</th><td>Popraw widoczność przycisku i możliwość obsługi formularza z otwartą klawiaturą.</td><td>Czy da się zakończyć cały scenariusz na rzeczywistym urządzeniu?</td></tr>
</tbody></table></div>
<p>Odróżniaj kliknięcie przycisku od wysłanej wiadomości i od zapytania, które firma może obsłużyć. Zapisz datę zmiany, źródła ruchu oraz ewentualne zmiany budżetu i oferty. Przy małej liczbie wizyt porównanie kilku pojedynczych zgłoszeń nie daje pewnego rozstrzygnięcia; pomocne będą wtedy rozmowy z odbiorcami i testy wykonania zadania.</p>
<p>Jeśli przygotowujesz <a href="{{link:/tworzenie-landing-page/}}">własny landing page</a>, wpisz sposób pomiaru do zakresu wdrożenia. Dobór formatu strony wyjaśnia poradnik <a href="{{link:/strony-internetowe/landing-page-co-to/}}">czym jest landing page</a>, a szerszy problem diagnozuje tekst <a href="{{link:/seo-i-konwersja/dlaczego-strona-ma-ruch-ale-nie-generuje-zapytan/}}">dlaczego strona ma ruch, ale nie generuje zapytań</a>.</p>
HTML,
                ],
            ],
        ],
        '/strony-www/strona-internetowa-dla-malej-firmy/' => [
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-mala-firma-zakres', 'after' => 'plan-na-start',
                    'toc' => 'Co zrobić teraz, co rozbudować później', 'label' => 'Priorytety małej firmy',
                    'title' => 'Jak ograniczyć zakres na start i zostawić miejsce na rozwój?',
                    'html' => <<<'HTML'
<p>Mała firma nie musi uruchamiać wszystkich pomysłów razem. Po ustaleniu podstawowych podstron rozdziel funkcje według tego, czy są potrzebne do obsługi najbliższych zapytań. Pomaga pytanie: „Czy bez tego elementu klient potrafi zrozumieć ofertę i się z nami skontaktować?”.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Priorytety strony małej firmy" tabindex="0"><table><thead><tr><th scope="col">Zakres</th><th scope="col">Na początek</th><th scope="col">Sygnał do rozbudowy</th></tr></thead><tbody>
<tr><th scope="row">Oferta</th><td>Opisy usług, które firma faktycznie chce sprzedawać.</td><td>Nowa grupa klientów potrzebuje osobnego wyjaśnienia zakresu.</td></tr>
<tr><th scope="row">Kontakt</th><td>Telefon i działający formularz z prostą instrukcją.</td><td>Ręczne ustalanie terminów staje się problemem — warto rozważyć rezerwacje.</td></tr>
<tr><th scope="row">Realizacje</th><td>Kilka własnych przykładów z opisem wykonanej pracy.</td><td>Większa liczba projektów wymaga podziału według usług.</td></tr>
<tr><th scope="row">Wiedza</th><td>Odpowiedzi na powtarzalne pytania przy ofercie.</td><td>Pojawiają się tematy, które zasługują na osobny poradnik.</td></tr>
</tbody></table></div>
<p>Przykładowo usługodawca dojeżdżający do klienta może zacząć od obszaru obsługi, opisu usługi, realizacji oraz kontaktu. Własny sklep lub rozbudowany panel rezerwacji ma uzasadnienie dopiero wtedy, gdy odpowiada sposobowi sprzedaży i obsługi. Przy <a href="{{link:/strona-wizytowka/}}">prostej stronie wizytówce</a> sprawdź wcześniej, czy później będzie można dodać samodzielne podstrony usług bez budowania całości od początku.</p>
<p>Ustal również, kto zajmie się stroną po uruchomieniu. Jedna osoba powinna aktualizować zakres usług, dane i realizacje oraz sprawdzać wiadomości z formularza. Pozostawienie bloga bez osoby odpowiedzialnej za treści nie pomoże odbiorcy znaleźć aktualnych informacji. Zacznij od materiałów, które jesteś w stanie utrzymać w dobrym stanie.</p>
<p>Techniczną drogę realizacji wybierzesz z poradnikiem <a href="{{link:/strony-internetowe/jak-stworzyc-strone-internetowa/}}">jak stworzyć stronę internetową</a>, a elementy oferty i kontaktu rozwiniesz w tekście <a href="{{link:/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/}}">co powinna zawierać strona firmowa</a>.</p>
HTML,
                ],
            ],
        ],
        '/strony-internetowe/rodzaje-stron-internetowych/' => [
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-rodzaje-kryteria', 'after' => 'technologia',
                    'toc' => 'Cel, struktura i technologia', 'label' => 'Porządek w nazwach',
                    'title' => 'Strona firmowa, one page i WordPress opisują różne rzeczy',
                    'html' => <<<'HTML'
<p>Porównując oferty, oddziel trzy decyzje: <strong>cel strony, strukturę treści oraz technologię</strong>. „Firmowa” mówi, komu i czemu ma służyć witryna. „One page” oznacza układ oparty na jednej stronie, a WordPress jest systemem do zarządzania treścią. Te określenia mogą dotyczyć jednego projektu, dlatego nie powinny tworzyć listy wzajemnie wykluczających się opcji.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Cel strony, struktura i sposób wdrożenia" tabindex="0"><table><thead><tr><th scope="col">Cel</th><th scope="col">Przykładowa struktura</th><th scope="col">Decyzja o wdrożeniu</th></tr></thead><tbody>
<tr><th scope="row">Prezentacja usług firmy</th><td>Strona główna i osobne opisy usług.</td><td>Wybór systemu, w którym zespół będzie edytować ofertę i realizacje.</td></tr>
<tr><th scope="row">Zapytania z jednej kampanii</th><td>Landing z jedną ofertą i konkretną drogą kontaktu.</td><td>Obsługa formularza, pomiar i możliwość poprawiania treści.</td></tr>
<tr><th scope="row">Prezentacja asortymentu</th><td>Katalog z kategoriami i kartami produktów.</td><td>Źródło danych, sposób aktualizacji i ewentualna rozbudowa o sprzedaż.</td></tr>
<tr><th scope="row">Umawianie wizyt</th><td>Oferta połączona z wyborem usługi lub terminu.</td><td>Integracja rezerwacji, dostępność terminów i odpowiedzialność za obsługę.</td></tr>
</tbody></table></div>
<p>Strona może też łączyć kilka funkcji. Firma usługowa może mieć wielostronicową witrynę, osobny landing do kampanii i rezerwacje przy jednej usłudze. Ważne, aby główna nawigacja pozostała zrozumiała, a każda część miała własne zadanie. Nie każdy katalog potrzebuje koszyka; nie każda strona z przyciskiem kontaktu jest landingiem kampanijnym.</p>
<p>O rozbudowie decyduje zmiana potrzeb. Osobna usługa z innymi odbiorcami może wymagać własnej podstrony, a duża liczba materiałów — podziału na kategorie i wyszukiwania. Zapisz takie przewidywane zmiany w briefie. Przykłady jednego celu omawiamy w artykule <a href="{{link:/seo-i-konwersja/landing-page-przyklady/}}">przykłady landing page</a>. Gdy wiesz już, jaki zakres jest potrzebny, przejdź do porównania sposobów <a href="{{link:/strony-internetowe/jak-stworzyc-strone-internetowa/}}">stworzenia strony internetowej dla firmy</a>.</p>
HTML,
                ],
            ],
        ],
        '/strony-internetowe/tworzenie-stron-internetowych-profesjonalny-proces-od-strategii-do-wdrozenia/' => [
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-proces-akceptacje', 'after' => 'wspolpraca',
                    'toc' => 'Materiały i kryteria akceptacji', 'label' => 'Współpraca w praktyce',
                    'title' => 'Co zatwierdzać na każdym etapie tworzenia strony?',
                    'html' => <<<'HTML'
<p>Harmonogram działa wtedy, gdy wiadomo, jaki materiał zamyka dany etap. Uzgodnij sposób zgłaszania uwag i jedną osobę, która zbierze decyzje po stronie firmy. Dzięki temu projektant nie dostaje kilku sprzecznych wersji oferty, a odbiór strony nie zamienia się w ponowne ustalanie jej celu.</p>
<div class="zpArticleNew__tableWrap" role="region" aria-label="Etapy tworzenia strony i kryteria akceptacji" tabindex="0"><table><thead><tr><th scope="col">Etap</th><th scope="col">Materiał od firmy</th><th scope="col">Co zatwierdzić</th></tr></thead><tbody>
<tr><th scope="row">Założenia</th><td>Cel, odbiorcy, oferta i wymagane funkcje.</td><td>Zakres projektu, odpowiedzialności i lista rezultatów.</td></tr>
<tr><th scope="row">Struktura</th><td>Podział usług i pytania klientów.</td><td>Listę podstron oraz drogę użytkownika do kontaktu.</td></tr>
<tr><th scope="row">Treści</th><td>Fakty o firmie, zdjęcia, opisy realizacji i dane.</td><td>Zgodność merytoryczną tekstów oraz komplet materiałów.</td></tr>
<tr><th scope="row">Projekt</th><td>Zebrane uwagi do przedstawionej koncepcji.</td><td>Wygląd najważniejszych widoków na komputerze i telefonie.</td></tr>
<tr><th scope="row">Wdrożenie</th><td>Osoby odbierające wiadomości i potrzebne dostępy.</td><td>Działanie formularzy, nawigacji, edycji oraz integracji.</td></tr>
<tr><th scope="row">Publikacja</th><td>Decyzję o terminie i osobę odpowiedzialną za utrzymanie.</td><td>Gotowość domeny, przekazanie dostępów i testy po uruchomieniu.</td></tr>
</tbody></table></div>
<div class="zpArticleGrowth__example"><p><strong>Przykład konkretnej uwagi:</strong> „Na telefonie na podstronie oferty po otwarciu klawiatury nie widzę przycisku wysłania formularza. Chcę zakończyć zapytanie bez zamykania pola. Załączam zrzut ekranu i model urządzenia”.</p><p>Taka informacja wskazuje miejsce, objaw i oczekiwane zachowanie. Przy uwadze do wyglądu dopisz, czy chodzi o czytelność, kolejność informacji czy zgodność z identyfikacją.</p></div>
<p>Zmiany zakresu zapisuj osobno od poprawek. Dodanie nowego języka lub kolejnej integracji wpływa na inne prace niż korekta tekstu w zatwierdzonym widoku. Przed startem przygotuj <a href="{{link:/strony-www/jak-przygotowac-brief-do-projektowania-strony-internetowej/}}">brief strony internetowej</a>, a przy odbiorze wykorzystaj scenariusze z poradnika <a href="{{link:/strony-internetowe/jak-stworzyc-strone-internetowa/}}">jak stworzyć i sprawdzić stronę firmową</a>. Konkretny zakres realizacji omawiamy na stronie <a href="{{link:/tworzenie-stron-internetowych/}}">tworzenie stron internetowych</a>.</p>
HTML,
                ],
            ],
        ],
        '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/' => [
            'text_fixes' => [
                // data-zp-local is appended by a later output buffer. Support
                // both the stored widget and an already transformed fragment.
                ['Jeśli projektujesz stronę od zera, zacznij od głównej usługi <a href="https://zaprojektowani.com/strony-internetowe-katowice/">strony internetowe Katowice</a>. To najważniejszy landing pod frazy związane z tworzeniem i projektowaniem stron internetowych dla firm.',
                 'Jeśli projektujesz stronę od zera, sprawdź zakres <a href="{{link:/tworzenie-stron-internetowych/}}">tworzenia stron internetowych dla firm</a>. Pokazujemy, jak łączymy strukturę, projekt, treści i wdrożenie oraz co warto przygotować przed rozpoczęciem współpracy.'],
                ['Jeśli projektujesz stronę od zera, zacznij od głównej usługi <a data-zp-local="1" href="https://zaprojektowani.com/strony-internetowe-katowice/">strony internetowe Katowice</a>. To najważniejszy landing pod frazy związane z tworzeniem i projektowaniem stron internetowych dla firm.',
                 'Jeśli projektujesz stronę od zera, sprawdź zakres <a href="{{link:/tworzenie-stron-internetowych/}}">tworzenia stron internetowych dla firm</a>. Pokazujemy, jak łączymy strukturę, projekt, treści i wdrożenie oraz co warto przygotować przed rozpoczęciem współpracy.'],
            ],
            'sections' => [
                [
                    'id' => 'zp-growth-20261017-firmowa-scenariusz', 'after' => 'oferta',
                    'toc' => 'Dwa scenariusze strony firmowej', 'label' => 'Oferta w praktyce',
                    'title' => 'Firma z kilkoma usługami i firma umawiająca wizyty potrzebują innej struktury',
                    'html' => <<<'HTML'
<p>Układ strony firmowej powinien odpowiadać sposobowi wyboru oferty. Gdy odbiorca porównuje kilka rodzajów usług, pomóż mu trafić do właściwej. Gdy zna usługę i chce wizyty, podaj warunki oraz wyraźną drogę umówienia. To zmienia kolejność informacji, nawet jeśli obie firmy mają sekcje „O nas”, „Oferta” i „Kontakt”.</p>
<h3>Kilka specjalizacji: od wyboru usługi do właściwego kontaktu</h3>
<p>W <a href="{{case:siemianowski}}">projekcie Kancelarii Siemianowski</a> ważny jest podział obszarów pomocy i dodatkowych funkcji serwisu. Dla podobnej firmy każda podstrona usługowa powinna wyjaśniać, kogo dotyczy oferta, co obejmuje i jakie informacje przygotować przed rozmową. Strona główna wskazuje drogę, a podstrona daje miejsce na konkretny opis.</p>
<h3>Wizyty: od informacji o usłudze do następnego kroku</h3>
<p>W projekcie Apartamentu Piękna oferta łączy się ze ścieżką umówienia wizyty. Przy podobnym modelu przydatne są informacje o przebiegu kontaktu, osobach wykonujących usługę i sposobie wyboru terminu. Przycisk powinien jasno mówić, co nastąpi po kliknięciu. Całość opisuje <a href="{{case:apartament}}">realizacja strony Apartamentu Piękna</a>.</p>
{{image:apartament-web}}
<p>Własną stronę sprawdź w dwóch scenariuszach: ktoś zna już firmę i szuka telefonu albo trafia pierwszy raz bezpośrednio na podstronę usługi. Obie osoby powinny dostać wystarczającą informację do kolejnego kroku. Materiały dobierz do tego zadania: własne zdjęcie zespołu, opis realizacji czy przykład procesu będą użyteczniejsze niż dekoracja bez związku z ofertą.</p>
HTML,
                ],
                [
                    'id' => 'zp-growth-20261017-firmowa-materialy', 'after' => 'porownanie',
                    'toc' => 'Materiały odpowiadające na pytania klienta', 'label' => 'Przygotowanie treści',
                    'title' => 'Jakie materiały zebrać, żeby strona firmowa odpowiadała na konkretne pytania?',
                    'html' => <<<'HTML'
<div class="zpArticleNew__tableWrap" role="region" aria-label="Pytania klientów i materiały do strony firmowej" tabindex="0"><table><thead><tr><th scope="col">Pytanie klienta</th><th scope="col">Miejsce odpowiedzi</th><th scope="col">Materiał do przygotowania</th></tr></thead><tbody>
<tr><th scope="row">Czy zajmujecie się moją sprawą?</th><td>Pierwszy ekran i podstrona usługi.</td><td>Dokładny zakres, odbiorca i ewentualny obszar obsługi.</td></tr>
<tr><th scope="row">Jak wygląda współpraca?</th><td>Opis procesu przy ofercie.</td><td>Etapy, obowiązki obu stron i sposób ustalenia terminu.</td></tr>
<tr><th scope="row">Co potwierdza doświadczenie?</th><td>Realizacje i informacje o zespole.</td><td>Własne zdjęcia, opis wykonanej pracy i sprawdzalne fakty.</td></tr>
<tr><th scope="row">Od czego zależy cena?</th><td>Zakres lub sekcja wyceny.</td><td>Elementy wpływające na koszt oraz dane potrzebne do oferty.</td></tr>
<tr><th scope="row">Co stanie się po wysłaniu zapytania?</th><td>Przy formularzu i w potwierdzeniu.</td><td>Rzeczywisty sposób obsługi oraz kolejny krok klienta.</td></tr>
</tbody></table></div>
<p>Odpowiedzi pisz własnymi słowami i aktualizuj, gdy zmienia się sposób obsługi. Jeśli wciąż trzeba tłumaczyć telefonicznie ten sam szczegół, dodaj go przy właściwej usłudze. Tak rozwijana <a href="{{link:/tworzenie-stron-internetowych/}}">strona internetowa dla firmy</a> staje się użytecznym wsparciem kontaktu. Przy ograniczonym zakresie pomoże też plan <a href="{{link:/strony-www/strona-internetowa-dla-malej-firmy/}}">strony dla małej firmy</a>.</p>
HTML,
                ],
            ],
        ],
    ],
];
