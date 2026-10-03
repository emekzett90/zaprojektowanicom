<?php
/**
 * ZP Suite v2.2.588 — /wiedza: szybsze pierwsze malowanie przy pełnym HTML dla indeksacji.
 * Wszystkie posty ZOSTAJĄ w initial HTML (posts_per_page -1 — ważne dla Google).
 * content-visibility:auto sprawia, że przeglądarka pomija layout/paint kart poza
 * viewportem i renderuje je dopiero przy scrollowaniu; contain-intrinsic-size
 * ("auto <fallback>") stabilizuje wysokość scrollbara, a po pierwszym renderze
 * zapamiętuje dokładny rozmiar karty. Obrazy kart mają już loading="lazy".
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_296_wiedza_fast_paint_css(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (strpos($uri, '/wiedza') === false && strpos($uri, '/blog') === false && strpos($uri, '/baza-wiedzy') === false) { return; }
  ?>
<style id="zp-suite-front-fixes-296-wiedza-fast-paint">
html body #zpKnowledgePro .zpKBPost{
  content-visibility:auto;
  contain-intrinsic-size:auto 420px;
}
@media (max-width:760px){
  html body #zpKnowledgePro .zpKBPost{
    contain-intrinsic-size:auto 360px;
  }
}
/* Sekcje archiwum miesięcznego poniżej fold — ten sam mechanizm */
html body #zpKnowledgePro .zpKBMonth{
  content-visibility:auto;
  contain-intrinsic-size:auto 520px;
}
/* Wyszukiwarka/filtry podmieniają listę przez JS — po podmianie karty są w viewporcie,
   c-v:auto renderuje je natychmiast; brak konfliktu. */
</style>
  <?php
}
add_action('wp_footer','zp_suite_296_wiedza_fast_paint_css',PHP_INT_MAX);
