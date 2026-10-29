<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.235
 * O nas: osobne, działające sterowanie odstępem sekcji i zdjęciem hero.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_head', function(){
  if (function_exists('zp_suite_233_is_about_page') && !zp_suite_233_is_about_page()) return;

  $get = function($key, $default){
    return (float) (function_exists('zp_suite_opt') ? zp_suite_opt($key, (string)$default) : $default);
  };

  $gap_d = max(70, min(460, $get('about_page.top_gap_desktop', 174)));
  $gap_m = max(56, min(380, $get('about_page.top_gap_mobile', 138)));

  // Przesuwa cały prawy visual / zdjęcie względem tekstu. Ujemnie = wyżej, dodatnio = niżej.
  $visual_y_d = max(-260, min(260, $get('about_page.hero_visual_y_desktop', 0)));
  $visual_y_m = max(-180, min(220, $get('about_page.hero_visual_y_mobile', 0)));

  // Dodatkowe kadrowanie samego obrazka wewnątrz visuala. Ujemnie = zdjęcie wyżej w kadrze.
  $img_y_d = max(-240, min(240, $get('about_page.hero_image_y_desktop', -50)));
  $img_y_m = max(-180, min(180, $get('about_page.hero_image_y_mobile', 0)));
  ?>
  <style id="zp-suite-front-fixes-235-about-controls">
    html body.zp-about-nav-final #zpAboutPage,
    html body:has(#zpAboutPage) #zpAboutPage{
      --zp-about-page-top-gap: <?php echo esc_html($gap_d); ?>px;
      --zp-about-hero-visual-y: <?php echo esc_html($visual_y_d); ?>px;
      --zp-about-hero-img-y: <?php echo esc_html($img_y_d); ?>px;
      padding-top:var(--zp-about-page-top-gap)!important;
    }

    /* Cały prawy box/zdjęcie — działa niezależnie od odstępu kickera. */
    html body.zp-about-nav-final #zpAboutPage .zpAboutPage__visual,
    html body:has(#zpAboutPage) #zpAboutPage .zpAboutPage__visual{
      transform:translate3d(0,var(--zp-about-hero-visual-y),0)!important;
      will-change:transform;
    }

    /* Kadrowanie obrazka w środku — nadpisuje wcześniejszy patch z translateY(-50px). */
    @media (min-width:1181px){
      html body.zp-about-nav-final #zpAboutPage .zpAboutPage__visual > img,
      html body:has(#zpAboutPage) #zpAboutPage .zpAboutPage__visual > img{
        transform:translate3d(0,var(--zp-about-hero-img-y),0)!important;
      }
      html body.zp-about-nav-final #zpAboutPage .zpAboutPage__visual::before,
      html body:has(#zpAboutPage) #zpAboutPage .zpAboutPage__visual::before{
        transform:translate3d(0,var(--zp-about-hero-img-y),0)!important;
      }
    }

    @media (max-width:1180px){
      html body.zp-about-nav-final #zpAboutPage,
      html body:has(#zpAboutPage) #zpAboutPage{
        --zp-about-page-top-gap: <?php echo esc_html($gap_m); ?>px;
        --zp-about-hero-visual-y: <?php echo esc_html($visual_y_m); ?>px;
        --zp-about-hero-img-y: <?php echo esc_html($img_y_m); ?>px;
        padding-top:var(--zp-about-page-top-gap)!important;
      }
      html body.zp-about-nav-final #zpAboutPage .zpAboutPage__visual,
      html body:has(#zpAboutPage) #zpAboutPage .zpAboutPage__visual{
        transform:translate3d(0,var(--zp-about-hero-visual-y),0)!important;
      }
      html body.zp-about-nav-final #zpAboutPage .zpAboutPage__visual > img,
      html body:has(#zpAboutPage) #zpAboutPage .zpAboutPage__visual > img{
        transform:translate3d(0,var(--zp-about-hero-img-y),0)!important;
        object-position:center top!important;
      }
    }
  </style>
  <?php
}, 2147483647);
