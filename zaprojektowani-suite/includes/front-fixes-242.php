<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.422 — Wiedza mobile hero dark background hardfix.
 * Keeps the /wiedza/ mobile hero visually consistent with dark service heroes
 * by forcing the dark navy base/overlay above the mobile video layer.
 */
function zp_suite_242_wiedza_mobile_dark_hero(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  $is_wiedza = false;
  if (function_exists('is_page') && is_page('wiedza')) { $is_wiedza = true; }
  if (!$is_wiedza && preg_match('~/(wiedza)(/|\?|#|$)~i', $uri)) { $is_wiedza = true; }
  if (!$is_wiedza) { return; }
  ?>
<style id="zp-suite-242-wiedza-mobile-dark-hero">@media (max-width: 880px){html body #zpKnowledgePro.zpKnowledgePro,html body #zpKnowledgePro.zpKnowledgePro .zpKBHero{background-color:#04080f!important}html body #zpKnowledgePro.zpKnowledgePro .zpKBHero{position:relative!important;isolation:isolate!important;overflow:hidden!important;color:#fff!important;background: radial-gradient(circle at 16% 10%,rgba(28,71,122,.42) 0%,rgba(28,71,122,0) 42%),radial-gradient(circle at 88% 8%,rgba(166,124,255,.14) 0%,rgba(166,124,255,0) 36%),radial-gradient(ellipse at 70% 42%,rgba(16,42,79,.30) 0%,rgba(16,42,79,0) 54%),linear-gradient(135deg,#020407 0%,#050b14 38%,#071426 68%,#030509 100%)!important}html body #zpKnowledgePro.zpKnowledgePro .zpKBHero__video{display:block!important;visibility:visible!important;opacity:.34!important;background:#04080f!important;z-index:0!important;mix-blend-mode:normal!important}html body #zpKnowledgePro.zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro.zpKnowledgePro .zpKBHero__videoEl{display:block!important;visibility:visible!important;opacity:.30!important;filter:saturate(.55) brightness(.42) contrast(1.14)!important;object-fit:cover!important;object-position:center!important;transform:scale(1.025)!important;mix-blend-mode:normal!important}html body #zpKnowledgePro.zpKnowledgePro .zpKBHero::before{content:""!important;position:absolute!important;inset:0!important;z-index:1!important;pointer-events:none!important;opacity:1!important;background: radial-gradient(circle at 18% 12%,rgba(28,71,122,.32) 0%,rgba(28,71,122,0) 38%),radial-gradient(circle at 82% 8%,rgba(166,124,255,.10) 0%,rgba(166,124,255,0) 34%),linear-gradient(rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(135deg,#020407 0%,#06101e 50%,#04080f 100%)!important;background-size:auto,auto,72px 72px,72px 72px,auto!important;-webkit-mask-image:none!important;mask-image:none!important;transform:none!important}html body #zpKnowledgePro.zpKnowledgePro .zpKBHero::after{content:""!important;position:absolute!important;inset:0!important;z-index:2!important;pointer-events:none!important;opacity:1!important;background: linear-gradient(90deg,rgba(2,4,7,.94) 0%,rgba(5,7,11,.76) 48%,rgba(2,4,7,.96) 100%),linear-gradient(180deg,rgba(2,4,7,.86) 0%,rgba(5,7,11,.58) 42%,rgba(0,0,0,.92) 100%)!important}html body #zpKnowledgePro.zpKnowledgePro .zpKBHero__inner,html body #zpKnowledgePro.zpKnowledgePro .zpKBHero__grid,html body #zpKnowledgePro.zpKnowledgePro .zpKBHero__copy,html body #zpKnowledgePro.zpKnowledgePro .zpKBHero__visual{position:relative!important;z-index:3!important;background:transparent!important}}</style>
  <?php
}
add_action('wp_footer', 'zp_suite_242_wiedza_mobile_dark_hero', 999);
