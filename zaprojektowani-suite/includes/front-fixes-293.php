<?php
/**
 * ZP Suite v2.2.582 — FAQ na podstronach: hover-fill 1:1 jak na stronie głównej.
 * Problem: na /strony-internetowe-katowice, /sklepy-internetowe-katowice i /logo-branding-katowice
 * gradient przy hoverze wypełniał tylko wewnętrzny przycisk pytania (zaokrąglony prostokąt
 * wsunięty 1px w głąb karty) — wyglądało to jak "bardziej zaokrąglone" gradientowe rogi,
 * nie pokrywające się z rogami kafla. Na home gradient dostaje cały __item (overflow:hidden),
 * więc wypełnia kartę idealnie do krawędzi.
 * Fix: hover (zamknięty item) → gradient na całym item, pytanie transparentne;
 * item otwarty → biała karta + gradient na pytaniu (jak dotychczas, top-radius only).
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_293_faq_hover_unify_css(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (
    zp_suite_service_kind($uri) === ''
  ) { return; }
  ?>
<style id="zp-suite-front-fixes-293-faq-hover-unify">
/* Hover na zamkniętym kaflu: gradient wypełnia CAŁĄ kartę (rogi 1:1 z kaflem, jak na home) */
html body .zpFaqKatNavy__item:hover:not(.is-open),
html body .zpFaqLogoKatNavy__item:hover:not(.is-open),
html body .zpFaqShopKatNavy__item:hover:not(.is-open){
  background:var(--navyGrad)!important;
  border-color:rgba(7,20,38,.30)!important;
}
/* Pytanie w hoverowanym (zamkniętym) kaflu nie maluje własnego, wsuniętego gradientu */
html body .zpFaqKatNavy__item:hover:not(.is-open) .zpFaqKatNavy__question,
html body .zpFaqLogoKatNavy__item:hover:not(.is-open) .zpFaqLogoKatNavy__question,
html body .zpFaqShopKatNavy__item:hover:not(.is-open) .zpFaqShopKatNavy__question{
  background:transparent!important;
}
/* Otwarty kafel: biała karta (odpowiedź na białym), gradient zostaje na pytaniu — bez zmian */
html body .zpFaqKatNavy__item.is-open,
html body .zpFaqLogoKatNavy__item.is-open,
html body .zpFaqShopKatNavy__item.is-open{
  background:#fff!important;
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_293_faq_hover_unify_css',PHP_INT_MAX);
