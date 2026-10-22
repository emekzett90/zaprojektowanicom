<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Article fixes applied when a post is displayed (2.5.0), so the Elementor data of Mat's
 * posts stays untouched and pausing the plan shows the posts as they were:
 *  - H1: the migration sets the plan's H1 as the post title, but Mat's articles have their
 *    <h1> written inside the article (article.zpArticleNew), so the visible heading kept the
 *    old text. The first <h1> of the article now shows the post title for posts whose plan
 *    entry sets an H1 (a title edited by hand in WordPress is shown as edited).
 *  - new sections from the content thread (data/sections.php), with a link in the article's
 *    table of contents,
 *  - links in posts that pointed to the wrong page: the old /wiedza/ landing page address
 *    (it redirects to a post about shops) and the "ampanie-…" typo address,
 *  - photos in the article are marked as article images, so they load in full size,
 *  - links from articles the plugin published to newer articles (2.8.0),
 *  - text fixes in articles the plugin published (2.8.2),
 *  - from the feed (feed.php, 2.9.0): new sections and links to new articles in older posts, and
 *    links to feed articles that are not published yet shown as plain text.
 * English pages are left alone.
 */

function zp_seo_posts_link_map(): array {
  $map = [
    '/meta-ads/ampanie-reklamowe-facebook-i-instagram-najczestsze-bledy/' => '/meta-ads/kampanie-reklamowe-facebook-i-instagram-najczestsze-bledy/',
    '/wiedza/jak-przygotowac-brief-do-projektowania-strony-internetowej/' => '/strony-www/jak-przygotowac-brief-do-projektowania-strony-internetowej/',
  ];
  if (zp_seo_plan_link_is_live('/tworzenie-landing-page/')) {
    $map['/wiedza/landing-page-pod-kampanie-meta-ads/'] = '/tworzenie-landing-page/';
    // 2.9.6: the post at this address showed the premium shop article; it redirects to the service page.
    $map['/sklepy-internetowe/landing-page-pod-kampanie-meta-ads/'] = '/tworzenie-landing-page/';
  }
  return $map;
}

/**
 * Links inside articles the plugin published (data/articles/), to articles from later
 * content batches: path => [[exact text in the article, the words that become the link, target]].
 * Nothing changes when the text was edited in WordPress or the target is not published.
 */
function zp_seo_posts_inline_links(): array {
  return [
    '/strony-internetowe/rodzaje-stron-internetowych/' => [
      ['<li>wizyty i noclegi: strona z systemem rezerwacji,</li>', 'systemem rezerwacji', '/strony-internetowe/system-rezerwacji-online/'],
    ],
    '/logo-branding/logo-wektorowe-pliki-logo/' => [
      ['Do ulotek, wizytówek, katalogów', 'wizytówek', '/logo-branding/projekt-wizytowki/'],
    ],
  ];
}

/**
 * Text fixes in articles the plugin already published (data/articles/ is used only when a post
 * is created): path => [[exact old text, new text]]. 2.8.2: the website price includes the domain
 * and hosting (Mat, 7.10). 2.9.6: the logo packages have as many concepts and revision rounds as
 * /projektowanie-logo/ and the Studio Wyceny list (4 and up to 5, 6 and 10, unlimited); two articles
 * still gave 4 and 6 concepts. 2.9.8: the Google Ads cost article said "~10 min" under the theme's
 * "11 min", and 3 000 zł ÷ 30,4 is about 99 zł, not 98. Nothing changes when the text was edited in
 * WordPress.
 */
function zp_seo_posts_text_fixes(): array {
  return [
    '/strony-internetowe/jak-stworzyc-strone-internetowa/' => [
      // Cost table, row "Agencja lub freelancer" (cell by cell: WordPress puts line breaks between cells).
      ['<td>projekt, u nas od 3 999 zł</td>', '<td>projekt, u nas od 3 999 zł z domeną i hostingiem</td>'],
      ['<td>domena, hosting i opcjonalnie opieka techniczna</td>', '<td>zwykle domena i hosting (u nas w cenie strony), opcjonalnie opieka techniczna</td>'],
      // FAQ "Ile kosztuje stworzenie strony internetowej?" (answer and JSON-LD).
      ['u nas projekty zaczynają się od 3 999 zł. Szczegóły opisuje poradnik',
       'u nas projekty zaczynają się od 3 999 zł, z domeną i hostingiem w cenie. Szczegóły opisuje poradnik'],
    ],
    '/kampanie-reklamowe/ile-kosztuje-google-ads/' => [
      ['<span>~10 min czytania</span><span>Google Ads • Budżet • Obsługa</span>', '<span>~11 min czytania</span><span>Google Ads • Budżet • Obsługa</span>'],
      ['podziel tę kwotę przez 30,4. Wychodzi około 98 zł i taką kwotę', 'podziel tę kwotę przez 30,4. Wychodzi około 99 zł i taką kwotę'],
      ['wynosi wtedy około 98 zł (3 000 zł ÷ 30,4)', 'wynosi wtedy około 99 zł (3 000 zł ÷ 30,4)'],
    ],
    '/logo-branding/projekt-wizytowki/' => [
      ['razem z 4 kierunkami logo, kartą stylu', 'razem z 6 koncepcjami logo, kartą stylu'],
      ['obejmuje m.in. 6 koncepcji logo, brandbook', 'obejmuje m.in. nielimitowaną liczbę koncepcji logo, brandbook'],
    ],
    '/logo-branding/logo-salonu-kosmetycznego/' => [
      // Package boxes in "Jak zamówić logo dla salonu kosmetycznego i ile to kosztuje?".
      ['4 wstępne propozycje logo, wariant kolorowy, mono i negatyw, pliki SVG do użycia online, avatar i cover do social mediów, mockupy oraz krótki poradnik użycia logo.',
       '4 autorskie koncepcje logo i do 5 rund poprawek, wariant kolorowy, mono i negatyw, pliki SVG, PDF, PNG i JPG, favicon i avatar do social mediów, 2–3 mockupy oraz instrukcja PDF użytkowania logo.'],
      ['4 kierunki logo, pliki AI i SVG z eksportami, karta stylu z kolorami, typografią i zasadami użycia, social pack, wizytówka, stopka e-mail i teczka firmowa, mockupy oraz przeniesienie praw.',
       '6 autorskich koncepcji logo i 10 rund poprawek, pliki AI i SVG z eksportami, karta stylu z kolorami, typografią i zasadami użycia, dwustronna wizytówka, stopka e-mail i teczka firmowa, 3–4 mockupy oraz przeniesienie praw autorskich.'],
      ['6 koncepcji logo, warianty znaku i pliki EPS oraz AI, karta stylu i brandbook z wytycznymi, mockupy premium, 6 szablonów social media, papier firmowy i komplet zasad marki.',
       'Nielimitowana liczba koncepcji logo i poprawek, wszystkie warianty znaku i pliki AI, EPS oraz SVG, pełna księga znaku z wytycznymi, papier firmowy, ulotka i szablon oferty, grafiki do social mediów, 6–8 mockupów premium oraz pełne prawa autorskie.'],
    ],
  ];
}

/**
 * 2.9.6: the logo package fixes of these posts also apply on the English page, where seo-271.php has
 * the English for the new texts (2.9.8: also the Google Ads cost article). The older fixes stay
 * Polish-only (their English comes from the dictionary entries of the old texts).
 */
function zp_seo_posts_text_fixes_en(string $html): string {
  if ($html === '' || !zp_seo_plan_active() || !zp_seo_plan_is_en() || is_admin() || !is_singular('post')) { return $html; }
  $id = (int) get_queried_object_id();
  if (!$id || (in_the_loop() && get_the_ID() !== $id)) { return $html; }
  $slug = (string) get_post_field('post_name', $id);
  foreach (['/logo-branding/projekt-wizytowki/', '/logo-branding/logo-salonu-kosmetycznego/', '/kampanie-reklamowe/ile-kosztuje-google-ads/'] as $path) {
    if (basename($path) !== $slug) { continue; }
    foreach (zp_seo_posts_text_fixes()[$path] as $fix) { $html = str_replace($fix[0], $fix[1], $html); }
  }
  return $html;
}

add_filter('the_content', 'zp_seo_posts_text_fixes_en', 21);
add_filter('elementor/frontend/the_content', 'zp_seo_posts_text_fixes_en', 21);

/**
 * SearchFit 14.10.2026, point 7: Clickmade moved its brief article, and the old address now answers 404. The brief post
 * (/logo-branding/brief-do-logo-i-identyfikacji-wizualnej/, and its English page, which shows the same content) links to
 * the article's current address when printed; the saved post stays as it is. Only this exact address changes.
 */
function zp_seo_posts_moved_links(string $html): string {
  if ($html === '' || strpos($html, 'clickmade.pl/blog/jak-przygotowac-brief-dla-grafika-aby-') === false) { return $html; }
  // The same address written with http, www or without the closing slash counts too.
  $old = '~https?://(?:www\.)?clickmade\.pl/blog/jak-przygotowac-brief-dla-grafika-aby-otrzymac-idealny-projekt(?=/|[\s"\'#?<]|$)~';
  return (string) (preg_replace($old, 'https://clickmade.pl/blog/jak-przygotowac-brief-dla-grafika-by-otrzymac-idealny-projekt', $html) ?? $html);
}

add_filter('the_content', 'zp_seo_posts_moved_links', 21);
add_filter('elementor/frontend/the_content', 'zp_seo_posts_moved_links', 21);

/**
 * Ahrefs 21.10.2026: the first $text outside <script> and <style> (its offset, or false). The transform runs twice on an
 * Elementor post (elementor/frontend/the_content, then the_content), and a fragment that was linked on the first run no
 * longer matches, so the second run took the next copy: the one in the post's own FAQ JSON-LD
 * (/logo-branding/identyfikacja-wizualna-firmy/), whose quotes then broke the JSON. Links go into visible text only.
 */
function zp_seo_posts_text_at(string $html, string $text) {
  $skip = [];
  if (stripos($html, '<script') !== false || stripos($html, '<style') !== false) {
    preg_match_all('~<(script|style)\b[^>]*>.*?</\1\s*>~is', $html, $m, PREG_OFFSET_CAPTURE);
    foreach ($m[0] as $block) { $skip[] = [$block[1], $block[1] + strlen($block[0])]; }
  }
  for ($from = 0; ($at = strpos($html, $text, $from)) !== false; $from = $at + 1) {
    $inside = false;
    foreach ($skip as [$a, $b]) { if ($at >= $a && $at < $b) { $inside = true; break; } }
    if (!$inside) { return $at; }
  }
  return false;
}

function zp_seo_posts_transform(string $html): string {
  static $busy = false;
  if ($busy || $html === '' || !zp_seo_plan_active() || zp_seo_plan_is_en() || is_admin() || !is_singular('post')) { return $html; }
  $id = (int) get_queried_object_id();
  if (!$id || (in_the_loop() && get_the_ID() !== $id)) { return $html; }
  $busy = true;
  $path = zp_seo_plan_path((string) get_permalink($id));

  // H1 inside the article.
  $entry = zp_seo_plan_entry($path) ?? [];
  $at = strpos($html, 'class="zpArticleNew');
  if ($at !== false && ($entry['kind'] ?? '') === 'post' && (string) ($entry['h1'] ?? '') !== '') {
    $title = trim((string) get_post_field('post_title', $id));
    if ($title !== '' && preg_match('~(<h1\b[^>]*>)(.*?)(</h1>)~is', $html, $m, PREG_OFFSET_CAPTURE, $at)) {
      $now = trim(html_entity_decode(wp_strip_all_tags($m[2][0]), ENT_QUOTES, 'UTF-8'));
      if ($now !== $title) {
        $html = substr($html, 0, $m[2][1]) . esc_html($title) . substr($html, $m[2][1] + strlen($m[2][0]));
      }
    }
  }

  // New sections and their table of contents links: after the section named in 'after' (3.1.0), otherwise
  // after the article's first section.
  foreach (array_merge((array) (zp_seo_plan_data('sections')[$path] ?? []), zp_feed_sections_for($path)) as $s) {
    $sid = (string) $s['id'];
    if (strpos($html, 'id="' . $sid . '"') !== false) { continue; }
    $range = !empty($s['after']) ? zp_seo_html_section_range($html, 'id="' . $s['after'] . '"') : null;
    if ($range === null && ($range = zp_seo_html_section_range($html, 'zpArticleNew__section')) === null) { continue; }
    $first = preg_match('~\bid="([^"]+)"~', substr($html, $range[0], 300), $fm) ? $fm[1] : '';
    $html = substr($html, 0, $range[1]) . "\n" . (string) $s['html'] . substr($html, $range[1]);
    if ($first !== '' && !empty($s['toc'])) {
      $html = (string) preg_replace('~(<nav\b[^>]*zpArticleNewTOC.*?<a\b[^>]*href="#' . preg_quote($first, '~') . '"[^>]*>.*?</a>)~is', '$1<a href="#' . esc_attr($sid) . '">' . esc_html((string) $s['toc']) . '</a>', $html, 1);
    }
  }

  // Photos in the article: the speed optimizer (includes/optimizer.php) recognises article
  // images by a "zpArticle" class on the <img>. Without it, a photo whose file name contains
  // e.g. "logo" was given the size hint of a small client logo (72px), so browsers loaded a
  // blurry thumbnail. Only the class is added; it carries no styles.
  $html = (string) preg_replace('~(<figure\b[^>]*\bzpArticleNew__imageBlock\b[^>]*>\s*)<img\b(?![^>]*\bclass=)~i', '$1<img class="zpArticleNew__img"', $html);

  // Links to newer articles.
  foreach ((array) (zp_seo_posts_inline_links()[$path] ?? []) as $link) {
    [$text, $words, $to] = $link;
    if (!zp_seo_plan_link_is_live($to) || ($at = zp_seo_posts_text_at($html, $text)) === false) { continue; }
    $linked = (string) preg_replace('~' . preg_quote($words, '~') . '~u', '<a href="' . esc_url(home_url($to)) . '">' . $words . '</a>', $text, 1);
    $html = substr_replace($html, $linked, $at, strlen($text));
  }

  // Links to articles from the feed: only where the exact fragment is still in the post.
  foreach (zp_feed_links_for($path) as $link) {
    [$text, $words, $to] = $link;
    if ($to === $path || !zp_seo_plan_link_is_live($to) || ($at = zp_seo_posts_text_at($html, $text)) === false) { continue; }
    $linked = zp_feed_link_fragment($text, $words, $to);
    if ($linked !== null) { $html = substr_replace($html, $linked, $at, strlen($text)); }
  }
  $html = zp_feed_unlink_pending($html);

  // Text fixes in published articles.
  foreach ((array) (zp_seo_posts_text_fixes()[$path] ?? []) as $fix) {
    $html = str_replace($fix[0], $fix[1], $html);
  }

  // Links to the wrong page.
  foreach (zp_seo_posts_link_map() as $from => $to) {
    $html = (string) preg_replace('~(\shref=["\'])(?:https?://(?:www\.)?zaprojektowani\.com)?' . preg_quote($from, '~') . '(?=[#?"\'])~i', '${1}' . esc_url(home_url($to)), $html);
  }
  $busy = false;
  return $html;
}

add_filter('the_content', 'zp_seo_posts_transform', 20);
add_filter('elementor/frontend/the_content', 'zp_seo_posts_transform', 20);

/**
 * 3.1.0: a post with a new or changed section in data/sections.php ('dated' => day of the change) gets a fresh
 * sitemap date and goes to IndexNow once, on the next administrator visit, as the feed additions do (ai.php).
 * Its text grew without an edit in WordPress, so nothing else would tell search engines to read it again.
 */
add_action('admin_init', function () {
  if (wp_doing_ajax() || !current_user_can('manage_options') || !zp_seo_plan_active() || !function_exists('zp_ai_on') || !zp_ai_on()) { return; }
  $seen = (array) get_option('zp_seo_sections_dated', []);
  $paths = [];
  foreach (zp_seo_plan_data('sections') as $path => $sections) {
    $day = max(array_merge([''], array_map(static fn($s) => (string) ($s['dated'] ?? ''), (array) $sections)));
    if ($day !== '' && $day > (string) ($seen[$path] ?? '')) { $paths[] = (string) $path; $seen[$path] = $day; }
  }
  if (!$paths) { return; }
  update_option('zp_seo_sections_dated', $seen, false);
  zp_ai_changed_at_display($paths, 'nowe sekcje we wpisach');
}, 110);

/**
 * 2.9.2: two of Mat's older posts still showed the notes of their brief: "[DO UZUPEŁNIENIA]"
 * table cells and "fill in before publishing" remarks (content thread, tresci/README.md,
 * "Problemy na stronie" 1). They are replaced when the post is displayed: table cells by row
 * (a cell starting with "[" gets the row's next value, the table keeps its markup), remarks by
 * their opening words up to the next tag. Prices are only the ones Mat confirmed; the second
 * post names no amounts, because providers' rates change. Nothing changes once the text is
 * edited in WordPress. Unlike zp_seo_posts_transform, this also runs on the English page
 * (seo-271.php has the English for the new texts).
 * 2.9.6 (article review of the AI visibility thread, widocznosc-ai/artykuly): four more posts with
 * leftovers of their brief, one with the opening paragraph and contents box of the website upkeep
 * post, and two with a section telling readers how the article helps Google and AI (#ai-seo). That
 * section now answers what the service costs, with the links it had (pointing at the current
 * addresses instead of old /wiedza/ ones).
 * Keys: post slug. 'when': text the post must contain (default: a "[DO UZUPEŁNIENIA]" or "przed
 * publikacją" note). 'rows': [regex matched against the row's text, cell values].
 * 'text': [opening words of a text, new text up to the next tag].
 * 'sections': [id => new HTML of the <section> with that id; only when the section still has the
 * 'when' text and one H2]. 'toc': [id => new text of the contents link to #id, changed with that section].
 */
function zp_seo_posts_placeholder_fixes(): array {
  return [
    'ile-kosztuje-stworzenie-strony-internetowej-dla-firmy' => [
      'rows' => [
        ['~rzeczywista oferta nr\W*1\b~u', ['Strona internetowa dla firmy', 'od 3 999 zł, domena i hosting w cenie', 'projekt, wdrożenie i wersja mobilna, zwykle w 2–4 tygodnie', 'cena rośnie z liczbą podstron, treściami, animacjami, SEO i integracjami']],
        ['~rzeczywista oferta nr\W*2\b~u', ['Sklep internetowy na WooCommerce', 'od 6 499 zł', 'projekt i wdrożenie sklepu', 'cena zależy od liczby produktów i kategorii, płatności, dostaw, wariantów i filtrów']],
        ['~rzeczywista oferta nr\W*3\b~u', ['Strona z funkcjami na zamówienie', 'wycena po briefie', 'np. kalkulator, wyszukiwarka, rezerwacje albo integracja z CRM', 'kwota zależy od liczby i złożoności funkcji']],
      ],
      'text' => [
        ['W tym miejscu najlepiej porównać trzy rzeczywiste zakresy', 'Poniżej porównujemy trzy zakresy z oferty Zaprojektowani: stronę firmową, sklep internetowy i stronę z funkcjami na zamówienie.'],
        ['Do uzupełnienia przed publikacją', 'Jak czytać tabelę'],
        ['Tabela cenowa ma zawierać 3 rzeczywiste zakresy', 'Kwoty w tabeli to ceny początkowe. Cenę dla swojego zakresu sprawdzisz w Studio Wyceny.'],
      ],
    ],
    'ile-kosztuje-utrzymanie-strony-internetowej' => [
      'rows' => [
        ['~^\s*Domena\s~u', ['opłata za rejestrację', 'opłata za odnowienie, często wyższa niż za rejestrację']],
        ['~^\s*Hosting\s~u', ['abonament roczny lub miesięczny', 'abonament roczny lub miesięczny']],
        ['~^\s*SSL\s~u', ['często w cenie hostingu', 'często w cenie hostingu']],
        ['~^\s*Licencje premium\s~u', ['zakup licencji, zwykle na rok', 'odnowienie licencji']],
        ['~^\s*Backup / monitoring\s~u', ['w hostingu albo w opiece technicznej', 'w hostingu albo w opiece technicznej']],
        ['~^\s*Opieka techniczna\s~u', ['abonament miesięczny', 'abonament miesięczny']],
        ['~^\s*Rozwój serwisu\s~u', ['według zakresu', 'według zakresu']],
      ],
      'text' => [
        ['W tabeli na końcu artykułu warto wpisać rzeczywiste stawki', 'Stawki rejestratorów domen, firm hostingowych i dostawców licencji zmieniają się co roku, dlatego tabela na końcu artykułu pokazuje rodzaj opłaty, a nie kwotę. Aktualną cenę sprawdzisz u swojego dostawcy.'],
        ['Poniższa tabela pokazuje, jak warto policzyć budżet. Nie wpisujemy', 'Poniższa tabela pokazuje, jak warto policzyć budżet: które opłaty wracają co roku, a które zależą od wybranych narzędzi i zakresu opieki.'],
        ['Przed publikacją tabelę trzeba uzupełnić', 'Porównując oferty, sprawdź, czy kwoty są netto czy brutto i za jaki okres.'],
      ],
    ],
    'ksiega-znaku-co-to-jest-co-zawiera-i-kiedy-firma-jej-potrzebuje' => [
      'text' => [
        ['Autorski przykład dokumentacji', 'Księga znaku w naszych pakietach'],
        ['Brief tego artykułu zakłada użycie', 'W Zaprojektowani pełną księgę znaku dostajesz w pakiecie Branding Premium za 2 999 zł, razem z plikami źródłowymi AI i EPS oraz wariantami znaku. Mini Branding za 1 499 zł ma zamiast niej kartę stylu z kolorami, typografią i zasadami użycia, a Logo Startowe za 999 zł krótką instrukcję użycia logo.'],
        ['Do uzupełnienia przed publikacją', 'Co obejmuje pełna księga znaku'],
        ['[DO UZUPEŁNIENIA PRZEZ REDAKCJĘ: wstawić rzeczywisty przykład', 'Wszystko, co opisujemy w tym artykule: wersje logo, pole ochronne, minimalne rozmiary, kolory, typografię oraz przykłady poprawnego i błędnego użycia.'],
        ['To szczególnie ważne przy frazie', 'Księgę przekazujemy razem z plikami logo, więc drukarnia, agencja czy nowy pracownik korzystają z jednego źródła zasad.'],
      ],
    ],
    'landing-page-co-to' => [
      'text' => [
        ['Do uzupełnienia przed publikacją', 'Kolejność sekcji na landingu'],
        ['[DO UZUPEŁNIENIA PRZEZ REDAKCJĘ: wstawić autorską makietę landingu', 'Od tej kolejności zaczynamy projekt landingu pod kampanię: pierwszy ekran z obietnicą i przyciskiem, dowody (opinie, logotypy klientów, liczby), oferta z ceną „od” i zakresem, odpowiedzi na obiekcje w FAQ, krótki formularz i powtórzone wezwanie do działania. Landing page projektujemy od 3 999 zł.'],
      ],
    ],
    'ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena' => [
      'text' => [
        ['Ceny i zakresy odpowiadają ofercie opisanej w materiale źródłowym', 'To aktualne ceny pakietów Zaprojektowani. Projekt logo trwa u nas zwykle 7–14 dni, a pełny zakres każdego pakietu sprawdzisz w Studiu Wyceny.'],
        // Package table and its three paragraphs: concepts, revision rounds and files as on /projektowanie-logo/.
        ['3 wstępne propozycje, wariant kolorowy', '4 autorskie koncepcje, do 5 rund poprawek, wariant kolorowy/mono/negatyw, SVG, PDF, PNG i JPG, favicon i avatar do social media, 2–3 mockupy, instrukcja PDF użytkowania logo, przeniesienie praw autorskich'],
        ['4 kierunki logo, AI + SVG, karta stylu', '6 autorskich koncepcji, 10 rund poprawek, AI + SVG, karta stylu z typografią, dwustronna wizytówka, stopka e-mail, teczka, favicon i avatar, 3–4 mockupy, instrukcja PDF i przeniesienie praw autorskich'],
        ['6 koncepcji, warianty znaku, EPS + AI', 'nielimitowane koncepcje i poprawki, analiza marki i konkurencji, wszystkie warianty znaku, AI + EPS + SVG, pełna księga znaku, wizytówka, papier firmowy, teczka, ulotka, szablon oferty, grafiki do social media, 6–8 mockupów premium, pełne prawa autorskie'],
        ['Logo Startowe jest rozwiązaniem dla marki, która', 'Logo Startowe jest rozwiązaniem dla marki, która przede wszystkim potrzebuje własnego profesjonalnego znaku i podstawowych plików do pierwszych wdrożeń. W aktualnej ofercie obejmuje 4 autorskie koncepcje logo, do 5 rund poprawek wybranego projektu, wariant kolorowy, monochromatyczny i negatyw, pliki SVG, PDF, PNG i JPG, paletę kolorów, favicon i avatar do social media, 2–3 mockupy, instrukcję PDF użytkowania logo oraz przeniesienie praw autorskich.'],
        ['Mini Branding rozszerza ten zakres o podstawowy system wizualny', 'Mini Branding rozszerza ten zakres o podstawowy system wizualny. Obejmuje 6 autorskich koncepcji logo, 10 rund poprawek wybranego kierunku, pliki AI, SVG, PDF, PNG i JPG, kartę stylu z kolorami i typografią, dwustronną wizytówkę, stopkę e-mail, teczkę firmową, 3–4 mockupy oraz przeniesienie praw autorskich. To poziom dla firmy, która chce od razu zachować spójność na stronie, w social mediach i materiałach sprzedażowych.'],
        ['Branding Premium jest przeznaczony dla marki potrzebującej', 'Branding Premium jest przeznaczony dla marki potrzebującej pełniejszego systemu. Aktualny zakres obejmuje nielimitowaną liczbę koncepcji logo i poprawek, analizę marki, branży i konkurencji, wszystkie warianty znaku, pliki AI, EPS, SVG, PDF, PNG i JPG, pełną księgę znaku z rozszerzoną kartą stylu, wizytówkę, papier firmowy, teczkę, stopkę e-mail, ulotkę i szablon oferty, avatar i grafiki cover do social media, 6–8 mockupów premium oraz pełne prawa autorskie.'],
      ],
    ],
    'agencja-brandingowa-czy-freelancer' => [
      'when' => 'ile kosztuje utrzymanie strony internetowej, najważniejsze',
      'text' => [
        ['Jeśli zastanawiasz się, ile kosztuje utrzymanie strony internetowej', 'Wybór między agencją brandingową a freelancerem zależy od zakresu projektu, nie od nazwy wykonawcy. Samo logo w prostym zakresie dobrze zrobi freelancer z pasującym portfolio, a logo z kartą stylu, materiałami firmowymi i księgą znaku łatwiej powierzyć studiu, które prowadzi cały proces. W Zaprojektowani projekt logo kosztuje od 999 zł i trwa zwykle 7–14 dni, a pełny system z księgą znaku to pakiet Branding Premium za 2 999 zł.'],
        // Contents box: the items of the upkeep post become this article's first three headings.
        ['Jakie koszty powracają co roku?', 'Odpowiedź zależy od zakresu projektu'],
        ['Domena i hosting: odnowienie a promocja', 'Logo, identyfikacja wizualna i branding'],
        ['Licencje i aktualizacje', 'Kiedy freelancer, a kiedy agencja?'],
        ['Poradnik zachowuje strukturę SEO/AEO', 'Piszemy z perspektywy studia, które od 2022 roku projektuje logo, identyfikację wizualną i strony internetowe dla firm.'],
      ],
    ],
    'ksiega-znaku-mini-brandbook-pelna-identyfikacja-wizualna-co-wybrac' => [
      'when' => 'pomaga Google i AI',
      'sections' => [
        'ai-seo' => '<section id="ai-seo" class="zpArticleNew__section"><p class="zpArticleNew__label">Ceny pakietów</p><h2>Ile kosztuje mini brandbook, księga znaku i pełna identyfikacja?</h2>
<p>W Zaprojektowani każdy z tych zakresów ma stałą cenę pakietu. Logo Startowe za 999 zł to logo z wariantami, plikami i krótką instrukcją użycia. Mini Branding za 1 499 zł dodaje kartę stylu z kolorami, typografią i zasadami użycia oraz wizytówkę, stopkę e-mail i teczkę firmową, czyli zakres mini brandbooka. Branding Premium za 2 999 zł to pełna identyfikacja z księgą znaku, plikami źródłowymi AI i EPS oraz grafikami do social mediów.</p>
<p>Projekt logo trwa u nas zwykle 7–14 dni. Pakiety i realizacje pokazujemy na stronie <a href="https://zaprojektowani.com/projektowanie-logo/">projektowanie logo</a>, a cały proces opisujemy w artykule <a href="https://zaprojektowani.com/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/">jak powstaje logo, które działa w internecie i w druku</a>. Jeśli identyfikacja ma od razu trafić na stronę i do reklam, przeczytaj, <a href="https://zaprojektowani.com/seo-i-konwersja/jak-polaczyc-branding-strone-reklamy-i-seo-w-jeden-system/">jak połączyć branding, stronę, reklamy i SEO</a>.</p>
</section>',
      ],
      'toc' => ['ai-seo' => 'Ceny pakietów'],
    ],
    'kampanie-facebook-ads-kiedy-warto-uruchomic-jak-przygotowac-strone' => [
      'when' => 'wzmacnia widoczność Zaprojektowani',
      'sections' => [
        'ai-seo' => '<section id="ai-seo" class="zpArticleNew__section"><p class="zpArticleNew__label">Koszt obsługi</p><h2>Ile kosztuje prowadzenie kampanii Facebook Ads?</h2>
<p>Obsługa kampanii Meta Ads kosztuje u nas od 1 200 zł miesięcznie i obejmuje strategię, kreacje, pomiar oraz optymalizację. Budżet mediowy płacisz Meta osobno, a kampanię możemy uruchomić w 7 dni. Jak policzyć budżet na test, pokazujemy w artykule <a href="https://zaprojektowani.com/meta-ads/ile-kosztuje-reklama-na-facebooku/">ile kosztuje reklama na Facebooku</a>, a zakres obsługi opisujemy na stronie <a href="https://zaprojektowani.com/kampanie-reklamowe/">kampanie Meta Ads</a>.</p>
<p>Jeśli strona nie jest jeszcze gotowa na ruch z reklam, zacznij od <a href="https://zaprojektowani.com/tworzenie-stron-internetowych/">strony internetowej</a> albo <a href="https://zaprojektowani.com/tworzenie-sklepow-internetowych/">sklepu internetowego</a> i sprawdź, <a href="https://zaprojektowani.com/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/">co powinna zawierać strona internetowa dla firmy</a>. Stronę projektujemy od 3 999 zł, z domeną i hostingiem w cenie.</p>
</section>',
      ],
      'toc' => ['ai-seo' => 'Koszt obsługi'],
    ],
  ];
}

/** A local schema identity URL as a normalized path, without its query or fragment. */
function zp_seo_posts_schema_path($url): string {
  if (!is_string($url) || $url === '') { return ''; }
  $host = (string) wp_parse_url($url, PHP_URL_HOST);
  $site_host = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
  if ($host !== '' && strcasecmp(preg_replace('~^www\.~i', '', $host), preg_replace('~^www\.~i', '', $site_host)) !== 0) { return ''; }
  $path = (string) wp_parse_url($url, PHP_URL_PATH);
  return $path !== '' && $path[0] === '/' ? zp_seo_plan_path($path) : '';
}

/** Six audited templates with an extra inline Article/BlogPosting; English is the same source post. */
function zp_seo_posts_duplicate_article_source(string $canonical): string {
  $paths = [
    '/logo-branding/brief-do-logo-i-identyfikacji-wizualnej/' => '/en/logo-branding/logo-and-visual-identity-brief/',
    '/logo-branding/logo-branding-i-strona-jaka-kolejnosc-wdrozenia-ma-sens/' => '/en/logo-branding/logo-branding-website-what-comes-first/',
    '/sklepy-internetowe/kategorie-produktow-w-sklepie-internetowym/' => '/en/online-stores/ecommerce-product-categories/',
    '/sklepy-internetowe/koszyk-w-sklepie-internetowym-jak-ograniczyc-porzucone-zakupy/' => '/en/online-stores/reduce-shopping-cart-abandonment/',
    '/poradniki/jak-pisac-teksty-na-strone-zeby-nie-brzmialy-jak-katalog-uslug/' => '/en/guides/how-to-write-website-copy-that-isnt-just-a-service-list/',
    '/strony-internetowe/projektowanie-stron-internetowych-co-decyduje-o-zapytaniach/' => '/en/websites/website-design-that-attracts-customers/',
  ];
  foreach ($paths as $source => $english) {
    if ($canonical === $source || $canonical === $english) { return $source; }
  }
  return '';
}

/** A positive current Rank Math article must have only current canonical identity URLs. */
function zp_seo_posts_current_rankmath_article(array $node, string $canonical): bool {
  if (!array_intersect((array) ($node['@type'] ?? []), ['Article', 'BlogPosting', 'NewsArticle'])) { return false; }
  $found = false;
  foreach (['@id', 'url', 'mainEntityOfPage'] as $key) {
    $value = $node[$key] ?? null;
    $urls = is_array($value) ? [$value['@id'] ?? null, $value['url'] ?? null] : [$value];
    foreach ($urls as $url) {
      if ($url === null || $url === '') { continue; }
      $found = true;
      if (zp_seo_posts_schema_path($url) !== $canonical) { return false; }
    }
  }
  return $found;
}

/** Two copied identities confirmed in the audit; never match a new or mixed unexpected identity. */
function zp_seo_posts_known_wrong_inline_identity(\stdClass $node, string $source, array $paths): bool {
  $paths = array_values(array_unique($paths));
  if (count($paths) !== 1) { return false; }
  $headline = $node->headline ?? '';
  if ($source === '/poradniki/jak-pisac-teksty-na-strone-zeby-nie-brzmialy-jak-katalog-uslug/') {
    return ($node->{'@type'} ?? '') === 'Article'
      && in_array($paths[0], ['/', '/en/'], true)
      && in_array($headline, ['Kiedy warto założyć sklep WooCommerce?', 'When is it worth starting a WooCommerce store?'], true);
  }
  if ($source === '/strony-internetowe/projektowanie-stron-internetowych-co-decyduje-o-zapytaniach/') {
    return ($node->{'@type'} ?? '') === 'BlogPosting'
      && in_array($paths[0], ['/strony-internetowe/projektowanie-stron-internetowych-co-decyduje-o-tym-czy-strona-zdobywa-klientow/', '/en/strony-internetowe/projektowanie-stron-internetowych-co-decyduje-o-tym-czy-strona-zdobywa-klientow/'], true)
      && in_array($headline, ['Projektowanie stron internetowych — co decyduje o tym, czy strona zdobywa klientów?', 'Website design — what determines whether a website wins clients?'], true);
  }
  return false;
}

/** Remove a primary inline duplicate only after Rank Math supplied the published post's current schema. */
function zp_seo_posts_redundant_current_article(\stdClass $node, string $canonical): bool {
  $source = zp_seo_posts_duplicate_article_source($canonical);
  if ($source === '' || empty($GLOBALS['zp_seo_posts_rankmath_article'][$source])) { return false; }
  $article = false;
  foreach ((array) ($node->{'@type'} ?? []) as $type) {
    if (is_string($type) && preg_match('~(?:^|[/#])(Article|BlogPosting|NewsArticle)$~', $type)) { $article = true; break; }
  }
  if (!$article) { return false; }
  $paths = [];
  $current = true;
  foreach (['@id', 'url', 'mainEntityOfPage'] as $key) {
    $value = $node->{$key} ?? null;
    $urls = $value instanceof \stdClass ? [$value->{'@id'} ?? null, $value->url ?? null] : [$value];
    foreach ($urls as $url) {
      if (is_string($url) && $url !== '') {
        $path = zp_seo_posts_schema_path($url);
        if ($path === '') { return false; }
        $paths[] = $path;
        if (zp_seo_posts_duplicate_article_source($path) !== $source) { $current = false; }
      }
    }
  }
  return $current || zp_seo_posts_known_wrong_inline_identity($node, $source, $paths);
}

/** Only old Article identity URLs count; a citation or a link in its text does not. */
function zp_seo_posts_legacy_article(\stdClass $node, string $canonical): bool {
  $types = (array) ($node->{'@type'} ?? []);
  $article = false;
  foreach ($types as $type) {
    if (is_string($type) && preg_match('~(?:^|[/#])(Article|BlogPosting|NewsArticle)$~', $type)) { $article = true; break; }
  }
  if (!$article) { return false; }
  $paths = [];
  foreach (['@id', 'url', 'mainEntityOfPage'] as $key) {
    $value = $node->{$key} ?? null;
    $urls = $value instanceof \stdClass ? [$value->{'@id'} ?? null, $value->url ?? null] : [$value];
    foreach ($urls as $url) {
      $path = zp_seo_posts_schema_path($url);
      if ($path !== '') { $paths[] = $path; }
    }
  }
  // An Article already identifying this post stays, even if another property contains an old URL.
  if ($canonical !== '' && in_array($canonical, $paths, true)) { return false; }
  foreach ($paths as $path) {
    if (preg_match('~^/(?:en/)?(?:wiedza|wiedza-strony-internetowe-seo-i-konwersja|wiedza-logo-branding|poradniki)/~', $path)) { return true; }
  }
  return false;
}

/** Prune stale Article nodes at any depth, retaining other schema nodes and JSON object/list types. */
function zp_seo_posts_prune_legacy_articles($data, string $canonical, bool &$changed, bool $primary_node = true, bool $remove_legacy = true) {
  if ($data instanceof \stdClass) {
    if (($remove_legacy && zp_seo_posts_legacy_article($data, $canonical)) || ($primary_node && zp_seo_posts_redundant_current_article($data, $canonical))) { $changed = true; return null; }
    $had_graph = isset($data->{'@graph'}) && $data->{'@graph'} !== [];
    foreach ($data as $key => $value) {
      $filtered = zp_seo_posts_prune_legacy_articles($value, $canonical, $changed, $key === '@graph', $remove_legacy);
      if ($value instanceof \stdClass && $filtered === null) { unset($data->{$key}); }
      else { $data->{$key} = $filtered; }
    }
    // A graph holding only removed Articles has no schema left to publish.
    if ($had_graph && (!isset($data->{'@graph'}) || $data->{'@graph'} === []) && !array_diff(array_keys(get_object_vars($data)), ['@context', '@graph', '@id'])) { return null; }
    return $data;
  }
  if (is_array($data)) {
    $out = [];
    foreach ($data as $value) {
      $filtered = zp_seo_posts_prune_legacy_articles($value, $canonical, $changed, $primary_node, $remove_legacy);
      if (!($value instanceof \stdClass && $filtered === null)) { $out[] = $filtered; }
    }
    return $out;
  }
  return $data;
}

/** Keep the script byte-for-byte unless a confirmed stale or positively backed duplicate Article was removed. */
function zp_seo_posts_clean_legacy_schema(string $html, string $canonical, bool $remove_legacy = true): string {
  return (string) preg_replace_callback('~(<script\b[^>]*application/ld\+json[^>]*>)(.*?)(</script>)~is', static function (array $m) use ($canonical, $remove_legacy): string {
    $data = json_decode(trim($m[2]));
    if (json_last_error() !== JSON_ERROR_NONE) { return $m[0]; }
    $changed = false;
    $data = zp_seo_posts_prune_legacy_articles($data, $canonical, $changed, true, $remove_legacy);
    if (!$changed) { return $m[0]; }
    if ($data === null || $data === []) { return ''; }
    $json = wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    return is_string($json) ? $m[1] . $json . $m[3] : $m[0];
  }, $html);
}

/** A node of an old block: 'keep' (FAQPage, HowTo), the page-level type it is, 'service' or '' (left as it is). */
function zp_seo_posts_old_block_role($node): string {
  if (!$node instanceof \stdClass) { return ''; }
  $types = array_map('strval', (array) ($node->{'@type'} ?? []));
  if (array_intersect($types, ['FAQPage', 'HowTo'])) { return 'keep'; }
  if (in_array('WebPage', $types, true)) { return 'webpage'; }
  if (in_array('WebSite', $types, true)) { return 'website'; }
  if (in_array('BreadcrumbList', $types, true)) { return 'breadcrumb'; }
  if (array_intersect($types, ['Organization', 'LocalBusiness', 'ProfessionalService', 'Corporation'])) { return 'organization'; }
  // A Service in a post is a copy of the service page's one, with the old address.
  return in_array('Service', $types, true) ? 'service' : '';
}

/** References and own addresses of a kept node: old addresses of the post become $url, references to removed nodes the current ones. */
function zp_seo_posts_old_block_repoint($value, string $url, array $old_paths, array $removed, string $key = '') {
  if ($value instanceof \stdClass) {
    $props = get_object_vars($value);
    if (array_keys($props) === ['@id'] && is_string($props['@id']) && array_key_exists($props['@id'], $removed)) {
      return $removed[$props['@id']] === null ? null : (object) ['@id' => $removed[$props['@id']]];
    }
    foreach ($props as $k => $part) {
      $new = zp_seo_posts_old_block_repoint($part, $url, $old_paths, $removed, (string) $k);
      if ($new === null && $part !== null) { unset($value->{$k}); } else { $value->{$k} = $new; }
    }
    return $value;
  }
  if (is_array($value)) {
    $out = [];
    foreach ($value as $part) {
      $new = zp_seo_posts_old_block_repoint($part, $url, $old_paths, $removed, $key);
      if ($new !== null || $part === null) { $out[] = $new; }
    }
    return $out;
  }
  if (is_string($value) && in_array($key, ['@id', 'url', 'mainEntityOfPage', 'item'], true) && isset($old_paths[zp_seo_posts_schema_path($value)])) {
    $hash = strpos($value, '#');
    return $url . ($hash === false ? '' : substr($value, $hash));
  }
  return $value;
}

/**
 * SearchFit 14.10, point 3: six of Mat's posts (and their English pages) have a second, older JSON-LD block in the
 * article: WebPage, BreadcrumbList, FAQPage and HowTo, in two of them also WebSite, Organization and Service, under the
 * addresses the posts had before the move (now 301) and with the old titles. Rank Math describes the page, the site
 * and the business, and schema.php adds the breadcrumbs, so such a block keeps only FAQPage and HowTo, pointed at the
 * post's current address. When Rank Math's graph already has a FAQPage, the whole block goes. Only a block with one of
 * the page-level types is old; the FAQ-only blocks of the plugin's articles stay byte-for-byte.
 */
function zp_seo_posts_clean_old_blocks(string $html, string $url): string {
  $canonical = zp_seo_posts_schema_path($url);
  if ($canonical === '') { return $html; }
  $site = home_url('/');
  return (string) preg_replace_callback('~(<script\b[^>]*application/ld\+json[^>]*>)(.*?)(</script>)~is', static function (array $m) use ($url, $canonical, $site): string {
    $data = json_decode(trim($m[2]));
    if (json_last_error() !== JSON_ERROR_NONE) { return $m[0]; }
    $in_graph = $data instanceof \stdClass && isset($data->{'@graph'}) && is_array($data->{'@graph'});
    $nodes = $in_graph ? $data->{'@graph'} : (is_array($data) ? $data : [$data]);
    $roles = array_map('zp_seo_posts_old_block_role', $nodes);
    if (!array_intersect($roles, ['webpage', 'website', 'breadcrumb', 'organization'])) { return $m[0]; }
    if (!empty($GLOBALS['zp_seo_posts_rankmath_faq'])) { return ''; }
    // What the ids of removed nodes become (null: the reference goes), and the post's old addresses.
    $now = ['webpage' => $url . '#webpage', 'website' => $site . '#website', 'breadcrumb' => $url . '#breadcrumb', 'organization' => $site . '#organization', 'service' => null];
    $removed = [];
    $old_paths = [];
    foreach ($nodes as $i => $node) {
      if (array_key_exists($roles[$i], $now) && is_string($node->{'@id'} ?? null)) { $removed[$node->{'@id'}] = $now[$roles[$i]]; }
      if ($roles[$i] !== 'webpage' && $roles[$i] !== 'keep') { continue; }
      foreach (['@id', 'url'] as $key) {
        $path = zp_seo_posts_schema_path($node->{$key} ?? '');
        if ($path !== '' && $path !== '/' && $path !== $canonical) { $old_paths[$path] = true; }
      }
    }
    $kept = [];
    $faq = false;
    foreach ($nodes as $i => $node) {
      if ($roles[$i] !== 'keep' && $roles[$i] !== '') { continue; }
      $kept[] = zp_seo_posts_old_block_repoint($node, $url, $old_paths, $removed);
      if ($roles[$i] === 'keep' && in_array('FAQPage', array_map('strval', (array) ($node->{'@type'} ?? [])), true)) { $faq = true; }
    }
    if (!$kept) { return ''; }
    // Content rendered before wp_head: Rank Math's filter (below) then drops its own FAQPage, so the page keeps one.
    if ($faq && !isset($GLOBALS['zp_seo_posts_rankmath_faq'])) { $GLOBALS['zp_seo_posts_inline_faq'] = true; }
    if ($in_graph) { $data->{'@graph'} = $kept; } else { $data = $kept; }
    $json = wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    return is_string($json) ? $m[1] . $json . $m[3] : $m[0];
  }, $html);
}

/**
 * Point 3: whether Rank Math's final graph (after the content modules at 120 and 125) has a FAQPage, for the content
 * filter. A post rendered before wp_head already kept the old block's FAQPage, so then the graph's one goes instead.
 */
add_filter('rank_math/json_ld', function ($data, $jsonld = null) {
  if (!is_array($data) || is_admin() || !zp_seo_plan_active() || !is_singular('post')) { return $data; }
  $faq = false;
  foreach ($data as $key => $node) {
    $types = is_array($node) ? array_map('strval', (array) ($node['@type'] ?? [])) : [];
    if (!in_array('FAQPage', $types, true)) { continue; }
    if (empty($GLOBALS['zp_seo_posts_inline_faq'])) { $faq = true; continue; }
    if (count($types) === 1) { unset($data[$key]); continue; }
    $data[$key]['@type'] = array_values(array_diff($types, ['FAQPage']));
    unset($data[$key]['mainEntity']);
  }
  $GLOBALS['zp_seo_posts_rankmath_faq'] = $faq;
  return $data;
}, 200, 2);

/** Attribute lookup for bounded head tags; no serialization of the surrounding HTML. */
function zp_seo_posts_schema_attribute(string $tag, string $name): ?string {
  if (!class_exists('WP_HTML_Tag_Processor')) { return null; }
  // The processor needs a complete raw-text element to expose SCRIPT's opening attributes.
  $fragment = preg_match('~^<script\b~i', $tag) ? $tag . '</script>' : $tag;
  $processor = new \WP_HTML_Tag_Processor($fragment);
  if (!$processor->next_tag()) { return null; }
  $value = $processor->get_attribute($name);
  return is_string($value) ? $value : null;
}

/** Inspect the actual emitted Rank Math head, including canonical, instead of relying on render order. */
function zp_seo_posts_head_article_canonical(string $head, string $source): string {
  preg_match_all('~<link\b[^>]*>~is', $head, $links);
  $canonicals = [];
  foreach ($links[0] as $tag) {
    $rel = strtolower((string) zp_seo_posts_schema_attribute($tag, 'rel'));
    if (in_array('canonical', preg_split('~\s+~', trim($rel)) ?: [], true)) { $canonicals[] = zp_seo_posts_schema_path(zp_seo_posts_schema_attribute($tag, 'href')); }
  }
  if (count($canonicals) !== 1 || zp_seo_posts_duplicate_article_source($canonicals[0]) !== $source) { return ''; }
  $canonical = $canonicals[0];
  preg_match_all('~(<script\b[^>]*>)(.*?)</script\s*>~is', $head, $scripts, PREG_SET_ORDER);
  $articles = 0;
  foreach ($scripts as $script) {
    if (strtolower(trim((string) zp_seo_posts_schema_attribute($script[1], 'type'))) !== 'application/ld+json') { continue; }
    $classes = preg_split('~\s+~', trim((string) zp_seo_posts_schema_attribute($script[1], 'class'))) ?: [];
    if (!in_array('rank-math-schema', $classes, true)) { continue; }
    $data = json_decode(trim($script[2]), true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) { return ''; }
    $nodes = isset($data['@graph']) && is_array($data['@graph']) ? $data['@graph'] : [$data];
    foreach ($nodes as $node) {
      if (!is_array($node) || !array_intersect((array) ($node['@type'] ?? []), ['Article', 'BlogPosting', 'NewsArticle'])) { continue; }
      if (!zp_seo_posts_current_rankmath_article($node, $canonical)) { return ''; }
      $articles++;
    }
  }
  return $articles === 1 ? $canonical : '';
}

/** Final body-only pass for Elementor templates that rendered their content before wp_head. */
function zp_seo_posts_clean_complete_document(string $html): string {
  if (!zp_seo_plan_active() || is_admin() || !is_singular('post')) { return $html; }
  $id = (int) get_queried_object_id();
  $source = $id ? zp_seo_posts_duplicate_article_source(zp_seo_posts_schema_path((string) get_permalink($id))) : '';
  if ($source === '' || get_post_status($id) !== 'publish') { return $html; }
  if (!preg_match('~<head\b[^>]*>(.*?)</head\s*>~is', $html, $head, PREG_OFFSET_CAPTURE)) { return $html; }
  $canonical = zp_seo_posts_head_article_canonical($head[1][0], $source);
  if ($canonical === '') { return $html; }
  $after_head = $head[0][1] + strlen($head[0][0]);
  if (!preg_match('~<body\b[^>]*>(.*)</body\s*>~is', $html, $body, PREG_OFFSET_CAPTURE, $after_head)) { return $html; }
  $existed = isset($GLOBALS['zp_seo_posts_rankmath_article'][$source]);
  $previous = $GLOBALS['zp_seo_posts_rankmath_article'][$source] ?? null;
  $GLOBALS['zp_seo_posts_rankmath_article'][$source] = true;
  try { $clean = zp_seo_posts_clean_legacy_schema($body[1][0], $canonical, false); }
  finally {
    if ($existed) { $GLOBALS['zp_seo_posts_rankmath_article'][$source] = $previous; }
    else { unset($GLOBALS['zp_seo_posts_rankmath_article'][$source]); }
  }
  if ($clean === $body[1][0]) { return $html; }
  return substr($html, 0, $body[1][1]) . $clean . substr($html, $body[1][1] + strlen($body[1][0]));
}

function zp_seo_posts_complete_document_buffer(string $html, int $phase): string {
  // An early flush can split the document; leave such chunks byte-for-byte unchanged.
  return ($phase & PHP_OUTPUT_HANDLER_FINAL) ? zp_seo_posts_clean_complete_document($html) : $html;
}

add_action('template_redirect', static function () {
  if (!zp_seo_plan_active() || is_admin() || !is_singular('post') || !empty($GLOBALS['zp_seo_posts_document_buffer'])) { return; }
  $id = (int) get_queried_object_id();
  $source = $id ? zp_seo_posts_duplicate_article_source(zp_seo_posts_schema_path((string) get_permalink($id))) : '';
  if ($source === '' || get_post_status($id) !== 'publish') { return; }
  $GLOBALS['zp_seo_posts_document_buffer'] = true;
  ob_start('zp_seo_posts_complete_document_buffer');
}, 99);

function zp_seo_posts_placeholders(string $html): string {
  if ($html === '' || !zp_seo_plan_active() || is_admin() || !is_singular('post')) { return $html; }
  $id = (int) get_queried_object_id();
  if (!$id || (in_the_loop() && get_the_ID() !== $id)) { return $html; }

  // Old Article JSON-LD also lives inside @graph and uses earlier category prefixes. Rank Math
  // supplies the current Article; keep inline FAQPage/HowTo siblings and canonical Articles.
  if (stripos($html, 'application/ld+json') !== false) {
    $html = zp_seo_posts_clean_legacy_schema($html, zp_seo_posts_schema_path((string) get_permalink($id)));
    // SearchFit 14.10, point 3: the old second block (page-level nodes under the pre-move address).
    $html = zp_seo_posts_clean_old_blocks($html, (string) get_permalink($id));
  }

  $fix = zp_seo_posts_placeholder_fixes()[(string) get_post_field('post_name', $id)] ?? null;
  if ($fix === null) { return $html; }
  $when = (string) ($fix['when'] ?? '');
  if ($when !== '' ? strpos($html, $when) === false : (strpos($html, '[DO UZUPE') === false && stripos($html, 'przed publikacj') === false)) { return $html; }

  // Whole sections, found by their id; skipped when the section was edited or holds more than one H2.
  $replaced = [];
  foreach ((array) ($fix['sections'] ?? []) as $sid => $new) {
    $range = zp_seo_html_section_range($html, 'id="' . $sid . '"');
    if ($range === null) { continue; }
    $old = substr($html, $range[0], $range[1] - $range[0]);
    if (($when !== '' && strpos($old, $when) === false) || substr_count(strtolower($old), '<h2') !== 1) { continue; }
    $html = substr($html, 0, $range[0]) . $new . substr($html, $range[1]);
    $replaced[$sid] = true;
  }
  // The contents link changes only together with its section.
  foreach ((array) ($fix['toc'] ?? []) as $sid => $label) {
    if (empty($replaced[$sid])) { continue; }
    $html = (string) preg_replace_callback('~(<a\b[^>]*href="#' . preg_quote((string) $sid, '~') . '"[^>]*>)((?:(?!</a>).)*?)([^<>]*)(</a>)~is', static function (array $m) use ($label): string {
      return trim($m[3]) === '' ? $m[0] : $m[1] . $m[2] . esc_html((string) $label) . $m[4];
    }, $html, 1);
  }

  if (!empty($fix['rows'])) {
    $html = (string) preg_replace_callback('~<tr\b[^>]*>.*?</tr>~is', static function (array $m) use ($fix): string {
      $text = html_entity_decode(wp_strip_all_tags(str_replace('<', ' <', $m[0])), ENT_QUOTES, 'UTF-8');
      if (strpos($text, '[') === false) { return $m[0]; }
      foreach ($fix['rows'] as [$re, $values]) {
        if (!preg_match($re, $text)) { continue; }
        return (string) preg_replace_callback('~(<t[dh]\b[^>]*>)(.*?)(</t[dh]>)~is', static function (array $c) use (&$values): string {
          $now = trim(html_entity_decode(wp_strip_all_tags($c[2]), ENT_QUOTES, 'UTF-8'));
          if ($now === '' || $now[0] !== '[' || !$values) { return $c[0]; }
          return $c[1] . esc_html((string) array_shift($values)) . $c[3];
        }, $m[0]);
      }
      return $m[0];
    }, $html);
  }

  foreach ((array) ($fix['text'] ?? []) as [$from, $to]) {
    // A space in the opening words also matches a non-breaking space.
    $re = str_replace(' ', '(?:\s|&nbsp;|&#160;)+', preg_quote($from, '~'));
    $html = (string) preg_replace('~' . $re . '[^<]*~u', str_replace(['\\', '$'], ['\\\\', '\$'], esc_html($to)), $html, 1);
  }
  // Whatever the rows above did not catch (a table built differently) still shows no note.
  return (string) preg_replace('~\[(?:DO UZUPEŁNIENIA[^\]<]*|kwota \+ netto/brutto \+ data|np\. liczba podstron[^\]<]*|co nie wchodzi w zakres|rzeczywisty zakres|ograniczenia)\]~u', '–', $html);
}

add_filter('the_content', 'zp_seo_posts_placeholders', 21);
add_filter('elementor/frontend/the_content', 'zp_seo_posts_placeholders', 21);

/**
 * 2.9.6: thirteen of Mat's September posts show "Ostatnia aktualizacja: 2026-09-16" under a
 * publication date of 17–21.09.2026, so a page looked updated before it was published. When the
 * shown date is earlier than the publication date, the line shows the post's last change in
 * WordPress, or the publication date for a post that was scheduled and not edited since. Also runs on the English page, where
 * "Last updated:" keeps the same date format.
 */
function zp_seo_posts_update_date(string $html): string {
  if ($html === '' || stripos($html, 'Ostatnia aktualizacja') === false || !zp_seo_plan_active() || is_admin() || !is_singular('post')) { return $html; }
  $id = (int) get_queried_object_id();
  if (!$id || (in_the_loop() && get_the_ID() !== $id)) { return $html; }
  $published = (string) get_the_date('Y-m-d', $id);
  // A scheduled post keeps the modified date of its last save before publication: show the later date.
  $shown = max($published, (string) get_the_modified_date('Y-m-d', $id));
  if ($published === '') { return $html; }
  return (string) preg_replace_callback('~(Ostatnia aktualizacja:\s*(?:</[a-z]+>)?(?:\s|&nbsp;|&#160;)*)(\d{4}-\d{2}-\d{2})~u', static function (array $m) use ($published, $shown): string {
    return $m[2] < $published ? $m[1] . $shown : $m[0];
  }, $html);
}

add_filter('the_content', 'zp_seo_posts_update_date', 21);
add_filter('elementor/frontend/the_content', 'zp_seo_posts_update_date', 21);
