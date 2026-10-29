<?php
/**
 * ZP Suite v2.2.491 — safe home brand card full image + 15% size.
 * v2.2.777 — image: Zgórecki Nieruchomości branding (zgorecki_bez_tla-2.webp), geometry retuned for its tighter crop.
 * v2.2.778 — Zgórecki image 15% larger (desktop and mobile).
 * Replaces only the home services card image and avoids thumbnail rewrites without output-buffer hacks.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_head', function () {
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-287-home-brand-card-full-safe-v491">html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery img{content:url('https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki_bez_tla-2.webp')!important;max-width:none!important;image-rendering:auto!important;object-fit:contain!important;object-position:center center!important}@media (min-width:861px){html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery{left:-2%!important;right:-4%!important;top:2%!important;bottom:8%!important}html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery img{transform:translateZ(24px) scale(1.15)!important}html body #zpShowcaseServices .zpSSCard--brand:hover .zpSSCard__mock--brandStationery img{transform:translateZ(34px) scale(1.185)!important}}@media (max-width:860px){html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__visual{min-height:395px!important;height:395px!important;margin:0 -42px -52px!important}html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery{left:-3%!important;right:-3%!important;top:2%!important;bottom:12%!important}html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery img{transform:translateZ(18px) scale(1.15)!important}}@media (max-width:520px){html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__visual{min-height:368px!important;height:368px!important;margin:2px -44px -52px!important}html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery{left:-3%!important;right:-3%!important;top:2%!important;bottom:12%!important}html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery img{transform:translateZ(18px) scale(1.15)!important}}</style>
  <?php
}, PHP_INT_MAX);

add_action('wp_footer', function () {
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-287-home-brand-card-image-guard-v491">
  (function(){
    var full='https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki_bez_tla-2.webp';
    var bad=/-\d+x\d+\.webp(?:\?.*)?$/i;
    function fixOne(img){
      if(!img) return;
      try{
        img.classList.add('skip-lazy','no-lazy','no-litespeed-lazyload');
        img.setAttribute('data-no-lazy','1');
        img.setAttribute('data-skip-lazy','1');
        img.setAttribute('data-nitro-no-lazy','1');
        img.setAttribute('sizes','100vw');
        img.setAttribute('srcset',full+' 1800w');
        img.setAttribute('data-srcset',full+' 1800w');
        img.setAttribute('data-src',full);
        img.setAttribute('data-large_image',full);
        if((img.currentSrc&&bad.test(img.currentSrc)) || (img.src&&img.src!==full) || (img.getAttribute('src')||'')!==full){ img.src=full; img.setAttribute('src',full); }
      }catch(e){}
    }
    function fix(){
      var root=document.getElementById('zpShowcaseServices');
      if(!root) return;
      root.querySelectorAll('.zpSSCard--brand .zpSSCard__mock--brandStationery img').forEach(fixOne);
    }
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',fix,{once:true});}else{fix();}
    window.addEventListener('load',fix,{once:true,passive:true});
    setTimeout(fix,800);
  })();
  </script>
  <?php
}, PHP_INT_MAX);
