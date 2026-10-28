<?php
if (!defined('ABSPATH')) { exit; }
$en = zp_home_refresh_english();
$items = array_values(array_filter(zp_suite_cms_get('faq', []), static function($row) { return !isset($row['visible']) || (string)$row['visible'] !== '0'; }));
$items = zp_home_faq_294_items($items);
// Audit 21.10.2026: on English pages the texts this template writes in English itself (the labels below and the fallback
// rows) are skipped by the language runtime, which counted them as missing translations and kept /en/ noindex. Rows from
// the CMS are Polish and still go through the runtime's dictionary.
$own = !$items;
if (!$items) { $items = zp_home_faq_20261016($en); }
$skip = $en ? ' data-zpl-skip=""' : '';
$skipRows = $en && $own ? ' data-zpl-skip=""' : '';
$t = static function($pl, $english) use ($en) { return $en ? $english : $pl; };
$url = static function($path) use ($en) { return $en && class_exists('ZPL\\Router') ? \ZPL\Router::url($path, 'en') : $path; };
$canonical = zp_home_faq_20261016($en);
$serviceLinks = [1=>'/tworzenie-stron-internetowych/',4=>'/tworzenie-sklepow-internetowych/',5=>'/projektowanie-logo/',9=>'/opieka-wordpress/',10=>'/kampanie-reklamowe/'];
?>
<link rel="stylesheet" href="<?php echo esc_url(ZP_SUITE_URL . 'assets/css/home-refresh-20261016.css?ver=' . ZP_SUITE_VERSION); ?>">
<section class="zpHF24" id="zpHomeFaqKatNavy" aria-labelledby="zpHF24Title" itemscope itemtype="https://schema.org/FAQPage" data-zp-home-refresh="3.2.24">
 <div class="zpHR24__inner zpHF24__layout">
  <div class="zpHF24__intro">
   <?php echo zp_services_faq_person_html(); ?>
   <span class="zpHR24__kicker"<?php echo $skip; ?>><?php echo $t('Przed startem • konkretnie i bez niedomówień','Before we start • clear answers'); ?></span>
   <h2 id="zpHF24Title"<?php echo $skip; ?>><?php echo $t('Twój projekt.<br><span>Twoje pytania.</span>','Your project.<br><span>Your questions.</span>'); ?></h2>
   <p<?php echo $skip; ?>><?php echo $t('Strona, sklep, logo czy kampania? Sprawdź, jak pracujemy, co obejmuje wycena i czego możesz oczekiwać na każdym etapie.','Website, store, logo or campaign? See how we work, what a quote includes and what to expect at each stage.'); ?></p>
   <a class="zpHR24__button" href="<?php echo esc_url($url('/studio-wyceny/')); ?>"<?php echo $skip; ?>><?php echo $t('Porozmawiajmy o projekcie','Let’s discuss your project'); ?> <span aria-hidden="true">↗</span></a>
   <a class="zpHR24__textLink" href="<?php echo esc_url($url('/najczesciej-zadawane-pytania/')); ?>"<?php echo $skip; ?>><?php echo $t('Przejdź do pełnego FAQ','Browse all FAQs'); ?> <span aria-hidden="true">↗</span></a>
  </div>
  <div class="zpHF24__items">
   <?php foreach ($items as $i => $item) :
    $q = trim((string)($item['q'] ?? '')); $a = trim((string)($item['a'] ?? '')); if ($q === '' || $a === '') { continue; }
   ?>
   <details class="zpHF24__item"<?php echo $skipRows; ?> <?php echo $i === 0 ? 'open' : ''; ?> itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
    <summary><span class="zpHF24__number" aria-hidden="true"><?php echo esc_html(str_pad((string)($i + 1),2,'0',STR_PAD_LEFT)); ?></span><span itemprop="name"><?php echo esc_html($q); ?></span><span class="zpHF24__plus" aria-hidden="true"></span></summary>
    <div class="zpHF24__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"><p itemprop="text"><?php echo !empty($item['html']) ? wp_kses($a,['a'=>['href'=>true,'class'=>true,'data-zp-local'=>true],'strong'=>[]]) : esc_html($a); ?></p>
    <?php if (isset($serviceLinks[$i], $canonical[$i]) && $q === $canonical[$i]['q']) : ?><a class="zpHR24__textLink" href="<?php echo esc_url($url($serviceLinks[$i])); ?>"<?php echo $skip; ?>><?php echo $t('Zobacz zakres usługi','Explore the service'); ?> <span aria-hidden="true">↗</span></a><?php endif; ?></div>
   </details>
   <?php endforeach; ?>
  </div>
 </div>
</section>
