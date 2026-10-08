<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Ciemny pas CTA stopki („Porozmawiajmy o projekcie”).
 *
 * - Zdjęcie zespołu nie jest już ukryte przed Google i czytnikami: ma opis (alt), podpis
 *   z imionami, wymiary (bez skoku układu) i srcset z rozmiarów WordPressa, gdy plik jest
 *   w bibliotece mediów, więc telefon nie pobiera pliku 2000 px.
 * - Liczby zaufania pod przyciskami biorą wartości z ustawień wtyczki (liczba realizacji,
 *   ocena Google) i te same „10+ lat doświadczenia zespołu” co hero strony głównej
 *   (zespół ma ponad 10 lat praktyki, studio działa od 2022 roku).
 * - Słowo „zaprojektowani” w tle jest rysowane z CSS (data-zp-mark), więc nie jest tekstem strony.
 * - Układ: na komputerze zdjęcie stoi po prawej i wychodzi nad pas, a tekst ma zarezerwowane
 *   miejsce obok (wcześniej przy 1100–1600 px zdjęcie zasłaniało nagłówek i opis); na tablecie
 *   i telefonie zdjęcie jest pod tekstem, całe (wcześniej ucinało Martę i Stanisława).
 * - Style mają selektor z #zpMegaFooter, żeby wygrać z warstwami footer.css i front-fixes-*.
 */

const ZP_FOOTER_CTA_REVIEWS = 'https://search.google.com/local/reviews?placeid=ChIJh0fbrHpbFkcRPEegRF9aX_I';

/** Attachment ID of the team photo URL (0 when the file is not in the media library). */
function zp_footer_cta_attachment_id(string $url): int {
  $key = 'zp_footer_cta_att_' . md5($url);
  $id = get_transient($key);
  if ($id === false) {
    $id = (int) attachment_url_to_postid($url);
    set_transient($key, (string) $id, DAY_IN_SECONDS);
  }
  return (int) $id;
}

/** <img> of the team photo with alt, dimensions and srcset when WordPress knows the file. */
function zp_footer_cta_team_img(string $url): string {
  $url = trim($url);
  if ($url === '') { return ''; }
  $attrs = [
    'src' => esc_url($url),
    'alt' => 'Marta, Mateusz i Stanisław z zespołu Zaprojektowani – studio stron internetowych i brandingu z Katowic',
    'loading' => 'lazy',
    'decoding' => 'async',
    'fetchpriority' => 'low',
  ];
  $id = zp_footer_cta_attachment_id($url);
  if ($id > 0) {
    $meta = wp_get_attachment_metadata($id);
    if (is_array($meta) && !empty($meta['width']) && !empty($meta['height'])) {
      $attrs['width'] = (string) (int) $meta['width'];
      $attrs['height'] = (string) (int) $meta['height'];
    }
    $srcset = wp_get_attachment_image_srcset($id, 'full', is_array($meta) ? $meta : null);
    if (is_string($srcset) && $srcset !== '') {
      $attrs['srcset'] = $srcset;
      $attrs['sizes'] = '(max-width: 760px) 100vw, (max-width: 1199px) 540px, 1020px';
    }
  }
  $html = '<img';
  foreach ($attrs as $name => $value) { $html .= ' ' . $name . '="' . esc_attr($value) . '"'; }
  return $html . '>';
}

/** Trust figures under the buttons (values from the plugin settings). */
function zp_footer_cta_proof(): array {
  $projects = function_exists('zp_seo_plan_projects_count') ? zp_seo_plan_projects_count() : (int) zp_suite_opt('stats.projects_count', 114);
  $rating = trim((string) zp_suite_opt('stats.google_rating', '5.0'));
  $items = [];
  if ($projects > 0) { $items[] = ['value' => $projects . '+', 'label' => 'projektów']; }
  if ($rating !== '') { $items[] = ['value' => $rating, 'star' => true, 'label' => 'ocena w Google', 'href' => ZP_FOOTER_CTA_REVIEWS]; }
  $items[] = ['value' => '10+', 'label' => 'lat doświadczenia zespołu'];
  return $items;
}

function zp_footer_cta_en_strings(): array {
  return [
    'Marta, Mateusz i Stanisław z zespołu Zaprojektowani – studio stron internetowych i brandingu z Katowic' => 'Marta, Mateusz and Stanisław of the Zaprojektowani team – a website and branding studio from Katowice, Poland',
    'Marta, Mateusz i Stanisław' => 'Marta, Mateusz and Stanisław',
    'zespół Zaprojektowani' => 'the Zaprojektowani team',
    'Zaprojektowani w liczbach' => 'Zaprojektowani in numbers',
    'projektów' => 'projects',
    'ocena w Google' => 'Google rating',
    'lat doświadczenia zespołu' => 'years of team experience',
  ];
}

$zp_footer_cta_overrides = static function ($value) {
  $value = is_array($value) ? $value : [];
  return $value + zp_footer_cta_en_strings();
};
add_filter('option_zpl_overrides', $zp_footer_cta_overrides);
add_filter('default_option_zpl_overrides', $zp_footer_cta_overrides);

add_action('wp_footer', function () {
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-footer-cta">
html body #zpMegaFooter .zpMegaFooter__darkBand--cta{--zpc-pt:clamp(76px,6.2vw,112px);--zpc-pb:clamp(56px,5vw,88px);--zpc-ph:clamp(640px,57vw,1020px);--zpc-pw:calc(var(--zpc-ph) * .98);--zpc-bleed:clamp(0px,calc((100vw - 1740px) / 2 - 40px),200px);--zpc-out:min(230px,calc(var(--zpc-ph) * .27));padding-top:var(--zpc-pt)!important;padding-bottom:var(--zpc-pb)!important;min-height:0!important}
html body #zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__darkBg{background:radial-gradient(42% 70% at 82% 78%,rgba(28,71,122,.30),rgba(28,71,122,0) 70%),radial-gradient(circle at 12% 0%,rgba(28,71,122,.10),rgba(0,0,0,0) 34%),linear-gradient(135deg,#030407 0%,#05070b 46%,#07101d 100%)!important}
html body #zpMegaFooter .zpMegaFooter__darkBand--cta::after{content:"";position:absolute;left:0;right:0;top:0;height:1px;background:linear-gradient(90deg,rgba(116,168,239,0) 0%,rgba(116,168,239,.28) 50%,rgba(116,168,239,0) 100%);pointer-events:none;z-index:2}
html body #zpMegaFooter .zpMegaFooter__cta{position:relative!important;display:grid!important;grid-template-columns:minmax(0,1fr)!important;grid-template-areas:"copy" "actions" "proof"!important;row-gap:clamp(26px,2.3vw,38px)!important;column-gap:0!important;align-items:start!important;justify-items:start!important;min-height:0!important}
html body #zpMegaFooter .zpMegaFooter__ctaCopy{grid-area:copy!important;min-width:0!important;max-width:840px!important;width:100%!important;padding:0!important;margin:0!important}
html body #zpMegaFooter .zpMegaFooter__cta h2{max-width:none!important;margin:0!important;font-size:clamp(38px,3.3vw,68px)!important;line-height:1.02!important;font-weight:500!important;letter-spacing:-.045em!important;word-spacing:.04em!important;text-wrap:balance}
html body #zpMegaFooter .zpMegaFooter__cta h2 span{display:block!important;width:fit-content!important;max-width:100%!important;margin-top:.24em!important;font-size:.74em!important;line-height:1.07!important;font-weight:650!important;letter-spacing:-.04em!important;color:#fff!important;background:linear-gradient(90deg,#fff 0%,#b7dcff 42%,#74a8ef 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;text-wrap:balance}
html body #zpMegaFooter .zpMegaFooter__ctaCopy>p{max-width:640px!important;margin:clamp(18px,1.6vw,26px) 0 0!important;font-size:clamp(15px,1.04vw,17.5px)!important;line-height:1.66!important;color:rgba(255,255,255,.68)!important}
html body #zpMegaFooter .zpMegaFooter__ctaActions{grid-area:actions!important;margin:0!important}
html body #zpMegaFooter .zpFootCta__proof{grid-area:proof;display:grid;grid-template-columns:repeat(3,auto);justify-content:start;gap:0;margin:0;padding:0;list-style:none;font-family:inherit}
html body #zpMegaFooter .zpFootCta__proof li{display:flex;flex-direction:column;align-items:flex-start;margin:0;padding:0 clamp(18px,1.7vw,30px);border-left:1px solid rgba(255,255,255,.14);list-style:none}
html body #zpMegaFooter .zpFootCta__proof li:first-child{padding-left:0;border-left:0}
html body #zpMegaFooter .zpFootCta__proof li>a{display:flex;flex-direction:column;align-items:inherit;color:inherit!important;text-decoration:none!important;transition:opacity .2s ease}
html body #zpMegaFooter .zpFootCta__proof li>a:hover{opacity:.82}
html body #zpMegaFooter .zpFootCta__proof b{display:flex;align-items:baseline;font-size:clamp(20px,1.45vw,24px);font-weight:650;letter-spacing:-.025em;line-height:1;color:#fff;font-variant-numeric:tabular-nums}
html body #zpMegaFooter .zpFootCta__proof b i{margin-left:4px;font-style:normal;font-size:.8em;line-height:1;color:#f5c451}
html body #zpMegaFooter .zpFootCta__proof span{margin-top:7px;font-size:13px;font-weight:500;line-height:1.2;color:rgba(255,255,255,.58);white-space:nowrap}
html body #zpMegaFooter .zpMegaFooter__teamPhoto{transform:none!important;max-width:none!important;padding:0!important;overflow:visible!important}
html body #zpMegaFooter .zpMegaFooter__teamPhoto img{position:absolute!important;inset:0!important;display:block!important;width:100%!important;height:100%!important;max-width:none!important;object-fit:contain!important;object-position:center bottom!important;transform:none!important;-webkit-mask-image:linear-gradient(180deg,#000 0%,#000 84%,rgba(0,0,0,.55) 93%,transparent 100%)!important;mask-image:linear-gradient(180deg,#000 0%,#000 84%,rgba(0,0,0,.55) 93%,transparent 100%)!important}
html body #zpMegaFooter .zpFootCta__team{position:absolute;left:50%;bottom:clamp(14px,1.6vw,26px);z-index:3;transform:translateX(-50%);display:flex;align-items:baseline;gap:7px;margin:0;padding:9px 16px;border:1px solid rgba(255,255,255,.14);border-radius:999px;background:rgba(7,20,38,.58);-webkit-backdrop-filter:blur(14px);backdrop-filter:blur(14px);white-space:nowrap;font-size:13px;line-height:1.2;color:rgba(255,255,255,.66);box-shadow:0 14px 36px -18px rgba(0,0,0,.7)}
html body #zpMegaFooter .zpFootCta__team strong{font-weight:650;color:#fff}
html body #zpMegaFooter .zpMegaFooter__mark--dark{left:50%!important;right:auto!important;bottom:-.1em!important;width:auto!important;max-width:none!important;height:auto!important;overflow:visible!important;clip-path:none!important;transform:translateX(-50%)!important;text-align:center!important;font-size:clamp(64px,12.2vw,250px)!important;line-height:.8!important;white-space:nowrap!important;pointer-events:none!important}
html body #zpMegaFooter .zpMegaFooter__mark--dark::before{content:attr(data-zp-mark)}
@media (min-width:1200px){
  html body #zpMegaFooter .zpMegaFooter__cta{padding-right:calc(var(--zpc-pw) - var(--zpc-bleed) + clamp(28px,3vw,64px))!important;min-height:calc(var(--zpc-ph) - var(--zpc-out) - var(--zpc-pt) - var(--zpc-pb))!important;align-content:center!important}
  html body #zpMegaFooter .zpMegaFooter__teamPhoto{grid-area:auto!important;position:absolute!important;top:auto!important;left:auto!important;right:calc(-1 * var(--zpc-bleed))!important;bottom:calc(-1 * var(--zpc-pb))!important;width:var(--zpc-pw)!important;height:var(--zpc-ph)!important;margin:0!important;justify-self:auto!important;z-index:80!important}
  html body #zpMegaFooter .zpMegaFooter__teamPhoto::before{left:50%!important;bottom:6%!important;width:86%!important;height:40%!important;opacity:.7!important}
}
@media (max-width:1199px){
  html body #zpMegaFooter .zpMegaFooter__darkBand--cta{--zpc-pt:clamp(48px,7vw,84px);--zpc-pb:clamp(40px,6vw,72px)}
  html body #zpMegaFooter .zpMegaFooter__cta{grid-template-areas:"copy" "photo" "actions" "proof"!important;padding-right:0!important;row-gap:28px!important}
  html body #zpMegaFooter .zpMegaFooter__ctaCopy{max-width:760px!important}
  html body #zpMegaFooter .zpMegaFooter__cta h2{font-size:clamp(40px,5.4vw,60px)!important}
  html body #zpMegaFooter .zpMegaFooter__teamPhoto{grid-area:photo!important;position:relative!important;top:auto!important;left:auto!important;right:auto!important;bottom:auto!important;justify-self:center!important;width:min(540px,100%)!important;height:auto!important;aspect-ratio:2006/2047;margin:4px 0 -6px!important;z-index:4!important}
}
@media (max-width:760px){
  html body #zpMegaFooter .zpMegaFooter__darkBand--cta{--zpc-pt:44px;--zpc-pb:40px}
  html body #zpMegaFooter .zpMegaFooter__cta{row-gap:24px!important;justify-items:stretch!important}
  html body #zpMegaFooter .zpMegaFooter__cta h2{font-size:clamp(31px,8.6vw,38px)!important;letter-spacing:-.04em!important;line-height:1.04!important}
  html body #zpMegaFooter .zpMegaFooter__cta h2 span{font-size:.8em!important;margin-top:.3em!important}
  html body #zpMegaFooter .zpMegaFooter__ctaCopy>p{font-size:15px!important;line-height:1.6!important;margin-top:16px!important}
  html body #zpMegaFooter .zpMegaFooter__teamPhoto{width:calc(100% + 20px)!important;max-width:480px!important;margin:0 -10px -4px!important}
  html body #zpMegaFooter .zpFootCta__team{bottom:10px;padding:8px 14px;font-size:12px}
  html body #zpMegaFooter .zpFootCta__proof{grid-template-columns:repeat(3,minmax(0,1fr));padding-top:4px}
  html body #zpMegaFooter .zpFootCta__proof li,html body #zpMegaFooter .zpFootCta__proof li:first-child{align-items:center;padding:0 6px;text-align:center}
  html body #zpMegaFooter .zpFootCta__proof span{font-size:12px;white-space:normal}
  html body #zpMegaFooter .zpMegaFooter__mark--dark{font-size:clamp(48px,13.6vw,104px)!important;bottom:-.08em!important}
}
@media (prefers-reduced-motion:reduce){html body #zpMegaFooter .zpFootCta__proof li>a{transition:none}}
</style>
  <?php
}, PHP_INT_MAX);
