<?php
/**
 * Referral beacon for "Widoczność AI → Wejścia z asystentów AI": seo-plan/ai.php counts it on
 * wp_ajax(_nopriv)_zp_suite_track.
 *
 * The old page-view statistics (a database write on every page view and a visitor id kept in the browser)
 * were removed in the panel clean-up. The beacon now runs only for a visit that comes from another site or
 * carries utm_source, sends only the address and the referrer, and stores nothing in the browser.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_footer', function(){
  if (is_admin() || !function_exists('zp_ai_on') || !zp_ai_on()) return;
  $ajax = admin_url('admin-ajax.php');
  ?>
  <script id="zp-suite-analytics-js">
  (function(){
    try{
      var ref=document.referrer||'', host='';
      try{ host=ref?new URL(ref).hostname:''; }catch(e){}
      if((!host||host===location.hostname) && !/[?&]utm_source=/i.test(location.search)) return;
      function send(){
        var data=new FormData();
        data.append('action','zp_suite_track');
        data.append('path',location.pathname+location.search);
        data.append('ref',ref);
        if(navigator.sendBeacon){ navigator.sendBeacon('<?php echo esc_url($ajax); ?>', data); }
        else { fetch('<?php echo esc_url($ajax); ?>',{method:'POST',body:data,keepalive:true,credentials:'same-origin'}).catch(function(){}); }
      }
      window.addEventListener('load',function(){ setTimeout(send, window.zpSuiteAnalyticsDelay || 2200); },{once:true,passive:true});
    }catch(e){}
  })();
  </script>
  <?php
}, 99);
