<?php
if (!defined('ABSPATH')) { exit; }
function zp_reviews_section_shortcode() {
 $snapshot = zp_reviews_snapshot(); $en = zp_home_refresh_english();
 $t = static function($pl,$english) use($en) { return $en ? $english : $pl; };
 // Audit 21.10.2026 (Mat: "tłumacz"): English pages show the reviews in English (assets/data/reviews-english-20261021.json,
 // keyed by review id; a review without an entry stays Polish) and the language runtime skips this section, which renders
 // its own English. The runtime counted the section's ~180 English labels as missing translations, so /en/ was noindex.
 static $english = null;
 if ($en && $english === null) { $decoded = json_decode((string) @file_get_contents(ZP_SUITE_PATH . 'assets/data/reviews-english-20261021.json'), true); $english = is_array($decoded) ? $decoded : []; }
 $quote = static function(array $r, string $polish) use($en, $english): array {
  $tr = $en ? trim((string)($english[(string)($r['id'] ?? '')] ?? '')) : '';
  return $tr !== '' ? [$tr, ' lang="en" title="Translated from Polish"'] : [$polish, ' lang="pl" translate="no"'];
 };
 $google = array_values(array_filter($snapshot['reviews'] ?? [],static function($r){ return ($r['source'] ?? '') === 'Google' && !empty($r['text']); }));
 $facebook = array_values(array_filter($snapshot['reviews'] ?? [],static function($r){ return ($r['source'] ?? '') === 'Facebook' && !empty($r['text']); }));
 $reviews = []; for($i=0;$i<max(count($google),count($facebook));$i++){ if(isset($google[$i])){$reviews[]=$google[$i];} if(isset($facebook[$i])){$reviews[]=$facebook[$i];} }
 $rating = (string)($snapshot['aggregate']['rating'] ?? ''); $count = (int)($snapshot['aggregate']['reviewCount'] ?? 0);
 if (!$reviews || !$count) { return ''; }
 $fbCount = (int)($snapshot['facebookVerified']['reviewCount'] ?? 62); $fbPercent = (int)($snapshot['facebookVerified']['recommendPercent'] ?? 100);
 $trust = 'https://www.trustindex.io/reviews/zaprojektowani.com'; $fb = 'https://www.facebook.com/zaprojektowanicom/reviews';
 $url = static function($path) use($en) { return $en && class_exists('ZPL\\Router') ? \ZPL\Router::url($path,'en') : $path; };
 $featured = null;
 foreach ($reviews as $review) { if ((string)($review['id'] ?? '') === '286386693') { $featured = $review; break; } }
 ob_start(); ?>
<link rel="stylesheet" href="<?php echo esc_url(ZP_SUITE_URL . 'assets/css/home-refresh-20261016.css?ver=' . ZP_SUITE_VERSION); ?>">
<section class="zpReviews24" id="zpRevEd" aria-labelledby="zpReviews24Title" data-zp-home-refresh="3.2.24"<?php echo $en ? ' data-zpl-skip=""' : ''; ?>>
 <div class="zpHR24__inner">
  <header class="zpReviews24__heading">
   <div><span class="zpHR24__kicker"><?php echo $t('Wybrane opinie klientów • o naszej współpracy','Selected client reviews • working together'); ?></span>
    <h2 id="zpReviews24Title"><?php echo $t('Dobry projekt.<br><span>Jeszcze lepsza współpraca.</span>','Thoughtful design.<br><span>A better collaboration.</span>'); ?></h2></div>

  </header>
  <div class="zpReviews24__proof">
   <a class="zpReviews24__rating" href="<?php echo esc_url($trust); ?>" target="_blank" rel="noopener nofollow">
    <img class="zpReviews24__trustLogo" src="<?php echo esc_url(ZP_SUITE_URL . 'assets/img/trustindex-wordmark-white.webp'); ?>" alt="Trustindex" width="160" height="50" loading="lazy" decoding="async">
    <div><strong><?php echo esc_html($en ? $rating : str_replace('.',',',$rating)); ?><small>/5</small></strong><span class="zpReviews24__stars" aria-hidden="true">★★★★★</span><span><?php echo esc_html($count); ?> <?php echo $t('opinii w Trustindex','reviews on Trustindex'); ?></span></div><span class="zpReviews24__open" aria-hidden="true">↗</span>
   </a>
   <a class="zpReviews24__rating zpReviews24__rating--fb" href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener nofollow">
    <img src="<?php echo esc_url(zp_suite_source_icon('Facebook')); ?>" alt="Facebook" width="46" height="46" loading="lazy" decoding="async">
    <div><strong><?php echo esc_html($fbPercent); ?><small>%</small></strong><span><?php echo $t('poleca nas na Facebooku','recommend us on Facebook'); ?></span><span><?php echo esc_html($fbCount).' '.$t('opinie','reviews'); ?></span></div><span class="zpReviews24__open" aria-hidden="true">↗</span>
   </a>
   <figure class="zpReviews24__team"><img src="<?php echo esc_url(ZP_SUITE_URL . 'assets/img/contact-team/zespol-rece-2x.webp'); ?>" alt="<?php echo esc_attr($t('Trzyosobowy zespół Zaprojektowani','The Zaprojektowani team')); ?>" width="941" height="960" loading="lazy" decoding="async"></figure>
  </div>
  <?php if ($featured): ?>
  <article class="zpReviews24__featured" data-zp-featured-review="<?php echo esc_attr($featured['id']); ?>" aria-label="<?php echo esc_attr($t('Wyróżniona opinia klienta','Featured client review')); ?>">
   <div class="zpReviews24__featuredBubble">
    <span class="zpHR24__kicker"><?php echo $t('Głos naszego klienta','In our client’s words'); ?></span>
    <span class="zpReviews24__quoteMark zpReviews24__quoteMark--open" aria-hidden="true">„</span>
    <?php [$featuredText, $featuredAttrs] = $quote($featured, (string)$featured['text']); ?><blockquote<?php echo $featuredAttrs; ?>><?php echo esc_html($featuredText); ?></blockquote>
    <span class="zpReviews24__quoteMark zpReviews24__quoteMark--close" aria-hidden="true">”</span>
   </div>
   <div class="zpReviews24__featuredMeta">
    <div class="zpReviews24__featuredAuthor"><img src="<?php echo esc_url(zp_suite_source_icon($featured['source'])); ?>" width="42" height="42" alt="<?php echo esc_attr($featured['source']); ?>" loading="lazy" decoding="async"><div><strong translate="no"><?php echo esc_html($featured['author']); ?></strong><span><?php echo $t('Poleca Zaprojektowani','Recommends Zaprojektowani'); ?> <span aria-hidden="true">·</span> <time datetime="<?php echo esc_attr($featured['displayDate']); ?>"><?php echo esc_html(date('d.m.Y', strtotime($featured['displayDate']))); ?></time></span></div></div>
    <a class="zpHR24__textLink" href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener nofollow"><?php echo $t('Zobacz opinie na Facebooku','See reviews on Facebook'); ?> <span aria-hidden="true">↗</span></a>
   </div>
  </article>
  <?php endif; ?>
  <div class="zpReviews24__navigation">
   <span class="zpReviews24__hint"><span class="zpReviews24__swipeHand" aria-hidden="true"><svg viewBox="0 0 26 28" fill="none"><path d="M9 14V5a2 2 0 0 1 4 0v7l1-1a2 2 0 0 1 3 1l1-1a2 2 0 0 1 3 2v5c0 4-2 7-6 7h-2c-2 0-3-1-4-3l-4-6a2 2 0 0 1 3-2l1 1"/><path d="M13 12v4m4-4v4"/></svg></span><?php echo $t('Przesuń w prawo — poznaj kolejne opinie','Swipe to explore more reviews'); ?></span>
   <span class="zpReviews24__position" data-zp-review-position><?php echo esc_html(count($reviews)).' '.$t('opinii klientów','client reviews'); ?></span>
   <div class="zpReviews24__arrows" data-zp-review-controls hidden>
    <button type="button" class="zpReviews24__arrow" data-zp-review-prev aria-label="<?php echo esc_attr($t('Poprzednie opinie','Previous reviews')); ?>" aria-controls="zpReviews24Rail" disabled><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5m6-6-6 6 6 6"/></svg></button>
    <button type="button" class="zpReviews24__arrow" data-zp-review-next aria-label="<?php echo esc_attr($t('Następne opinie','Next reviews')); ?>" aria-controls="zpReviews24Rail"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
    <button type="button" class="zpReviews24__arrow zpReviews24__autoplay" data-zp-review-autoplay aria-label="<?php echo esc_attr($t('Wstrzymaj rotację opinii','Pause review rotation')); ?>" data-pause-label="<?php echo esc_attr($t('Wstrzymaj rotację opinii','Pause review rotation')); ?>" data-play-label="<?php echo esc_attr($t('Wznów rotację opinii','Resume review rotation')); ?>"><span data-zp-review-pause-icon aria-hidden="true">Ⅱ</span><span data-zp-review-play-icon aria-hidden="true" hidden>▷</span></button>
   </div>
  </div>
  <div class="zpReviews24__rail" id="zpReviews24Rail" tabindex="0" role="region" aria-label="<?php echo esc_attr($t('Opinie klientów — przewijana lista','Client reviews — scrollable list')); ?>" data-zp-review-rail>
   <?php foreach($reviews as $i=>$r): $isFb=$r['source']==='Facebook'; $name=(string)$r['author']; [$text,$quoteAttrs]=$quote($r, str_replace('\\n', "\n", (string)$r['text'])); $parts=preg_split('/\s+/u',trim($text)); $long=count($parts)>43; $excerpt=$long?implode(' ',array_slice($parts,0,43)).'…':$text; $date=(string)($r['displayDate'] ?? ''); ?>
   <article class="zpReviews24__card" data-review-source="<?php echo esc_attr($r['source']); ?>">
    <header><span class="zpReviews24__avatar" aria-hidden="true"><?php echo esc_html(mb_strtoupper(mb_substr($name,0,1))); ?></span><div><h3 translate="no"><?php echo esc_html($name); ?></h3><time datetime="<?php echo esc_attr($date); ?>"><?php echo esc_html($date ? date('d.m.Y',strtotime($date)) : ''); ?></time></div><img src="<?php echo esc_url(zp_suite_source_icon($r['source'])); ?>" alt="<?php echo esc_attr($r['source']); ?>" width="28" height="28" loading="lazy" decoding="async"></header>
    <div class="zpReviews24__assessment"><?php if($isFb): ?><span class="zpReviews24__recommend" aria-hidden="true">✓</span> <?php echo $t('Poleca Zaprojektowani','Recommends Zaprojektowani'); ?><?php else: ?><span class="zpReviews24__stars" aria-label="<?php echo esc_attr((string)$r['rating'].'/5'); ?>"><?php echo esc_html(str_repeat('★',(int)$r['rating']).str_repeat('☆',5-(int)$r['rating'])); ?></span><?php endif; ?></div>
    <?php if($long): ?><details class="zpReviews24__readMore"><summary><span class="zpReviews24__excerpt"<?php echo $quoteAttrs; ?>><?php echo esc_html($excerpt); ?></span><span class="zpReviews24__expand"><span class="zpReviews24__expandClosed"><?php echo $t('Czytaj całą opinię','Read the full review'); ?></span><span class="zpReviews24__expandOpen"><?php echo $t('Zwiń opinię','Collapse the review'); ?></span> <span aria-hidden="true">＋</span></span></summary><blockquote<?php echo $quoteAttrs; ?>><?php echo nl2br(esc_html($text)); ?></blockquote></details><?php else: ?><blockquote<?php echo $quoteAttrs; ?>><?php echo nl2br(esc_html($text)); ?></blockquote><?php endif; ?>
    <a class="zpReviews24__source" href="<?php echo esc_url($isFb?$fb:$trust); ?>" target="_blank" rel="noopener nofollow"><?php echo $t('Opinie z ','Reviews from ').esc_html($r['source']); ?> <span aria-hidden="true">↗</span></a>
   </article>
   <?php endforeach; ?>
  </div>
  <footer class="zpReviews24__footer"><p><?php echo $t('Poznaj opinie. Zobacz realizacje. Zdecyduj o swoim projekcie.','Read the reviews. Explore the projects. Plan your next step.'); ?></p><a class="zpHR24__button" href="<?php echo esc_url($url('/realizacje/')); ?>"><?php echo $t('Zobacz nasze realizacje','Explore our work'); ?> <span aria-hidden="true">↗</span></a></footer>
 </div>
</section>
<script src="<?php echo esc_url(ZP_SUITE_URL . 'assets/js/home-reviews-20261016.js?ver=' . ZP_SUITE_VERSION); ?>" defer></script>
<?php return ob_get_clean();
}
add_shortcode('zp_reviews_section','zp_reviews_section_shortcode');
