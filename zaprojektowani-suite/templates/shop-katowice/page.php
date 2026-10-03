<?php
if (!defined('ABSPATH')) { exit; }
?>
<main id="zp-sklepy-internetowe-katowice" class="zpShopKatPage" data-zp-shop-katowice>
<?php foreach (zp_suite_shop_katowice_sections_map() as $zp_shop_key => $zp_shop_data) { echo zp_suite_shop_katowice_clean_html(zp_suite_shop_katowice_get_section($zp_shop_key)); } ?>
<?php
  // v2.2.18: dokładnie ten sam formularz kontaktowy co na podstronie /kontakt/.
  // Bez starego systemu ContactSystemLight i bez customowego override z CMS.
  echo '<!-- ZP Suite / formularz kontaktowy 1:1 z podstrony Kontakt -->' . do_shortcode('[zp_contact_system]');
?>
</main>
