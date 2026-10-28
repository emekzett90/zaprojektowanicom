<?php
/**
 * ZP Suite v2.2.827 — Meta pixel events sent from code.
 *
 * Replaces the 45 click rules of Meta's Event Setup Tool, which counted the same
 * person many times (every configurator click = InitiateCheckout, a WhatsApp tap = Lead,
 * a quote request = Purchase). After this module is live, those rules must be switched
 * off in Events Manager, otherwise events are counted twice.
 *
 * Consent: this module never loads the pixel. It only talks to the pixel that GTM starts
 * after Cookiebot marketing consent, and drops queued events when consent never comes.
 *
 * Events (pixel ZP_SUITE_META_PIXEL_ID):
 *  - ViewContent       service landing page (websites / shops / logo & branding), once per view.
 *  - InitiateCheckout  first interaction with the Studio Wyceny configurator, once per session.
 *  - Lead              Studio Wyceny thank-you page (once per order) and successful contact forms.
 *  - Contact           tap on phone / e-mail / WhatsApp / Messenger link, once per channel per view.
 *
 * The inline script must not contain the literal call/loader strings that the optimizer's
 * third-party delay buffer looks for, so the pixel function is referenced indirectly.
 */
if (!defined('ABSPATH')) { exit; }

if (!defined('ZP_SUITE_META_PIXEL_ID')) {
  define('ZP_SUITE_META_PIXEL_ID', '1911959386061443');
}

/**
 * v2.2.828 — event values (PLN). Meta flagged 100% of InitiateCheckout events as missing
 * value/currency. Logo packages have fixed prices; websites and shops use the budget the
 * client sets in Studio Wyceny, or these estimates when no budget is given. The values are
 * optimisation signals only and are never shown to visitors.
 */
function zp_suite_meta_default_values() {
  return (array) apply_filters('zp_suite_meta_default_values', [
    'strony'        => 6000,
    'sklepy'        => 6000,
    'logo-branding' => 999,
    'kampanie'      => 3000,
    'kontakt'       => 3000,
    'studio-wyceny' => 3000,
  ]);
}

function zp_suite_meta_lower($text) {
  return function_exists('mb_strtolower') ? mb_strtolower((string) $text, 'UTF-8') : strtolower((string) $text);
}

/** Logo package name => fixed price, taken from the Studio Wyceny package settings. */
function zp_suite_meta_brand_prices() {
  $out = [];
  if (!function_exists('zp_studio_packages')) { return $out; }
  $packages = zp_studio_packages();
  $rows = isset($packages['brand']['p']) && is_array($packages['brand']['p']) ? $packages['brand']['p'] : [];
  foreach ($rows as $row) {
    if (is_array($row) && !empty($row[0]) && isset($row[2]) && is_numeric($row[2]) && (int) $row[2] > 0) {
      $out[(string) $row[0]] = (int) $row[2];
    }
  }
  return $out;
}

/** First amount in a text such as "6 000 zł" or "3 000–5 000 zł"; 0 when there is none. */
function zp_suite_meta_first_amount($text) {
  if (!preg_match('/\d[\d\s\x{00A0}]*/u', (string) $text, $m)) { return 0; }
  return (int) preg_replace('/\D+/', '', $m[0]);
}

/** Value and category of a Studio Wyceny order, for the Lead event on its thank-you page. */
function zp_suite_meta_order_value($order) {
  $payload = isset($order['payload']) && is_array($order['payload']) ? $order['payload'] : [];
  $service = zp_suite_meta_lower(isset($payload['Usługa']) ? $payload['Usługa'] : '');
  $defaults = zp_suite_meta_default_values();
  if (strpos($service, 'logo') !== false || strpos($service, 'branding') !== false) {
    $package = zp_suite_meta_lower(isset($payload['Pakiet']) ? $payload['Pakiet'] : '');
    $value = 0;
    foreach (zp_suite_meta_brand_prices() as $name => $price) {
      if ($package !== '' && strpos($package, zp_suite_meta_lower($name)) !== false) { $value = $price; break; }
    }
    return ['value' => $value ?: (int) $defaults['logo-branding'], 'cat' => 'logo-branding'];
  }
  if (strpos($service, 'sklep') !== false) { $cat = 'sklepy'; }
  elseif (strpos($service, 'kampan') !== false || strpos($service, 'reklam') !== false) { $cat = 'kampanie'; }
  else { $cat = 'strony'; }
  $value = zp_suite_meta_first_amount(isset($payload['Budżet']) ? $payload['Budżet'] : '');
  if ($value < 100 || $value > 1000000) { $value = (int) $defaults[$cat]; }
  return ['value' => $value, 'cat' => $cat];
}

add_action('wp_footer', function () {
  if (is_admin() || !function_exists('zp_suite_is_frontend_request') || !zp_suite_is_frontend_request()) { return; }
  $pixel = preg_replace('/\D+/', '', (string) apply_filters('zp_suite_meta_pixel_id', ZP_SUITE_META_PIXEL_ID));
  if ($pixel === '') { return; }
  $cfg = [
    'pixel' => $pixel,
    'thank' => function_exists('zp_suite_is_thank_page_context') ? (bool) zp_suite_is_thank_page_context() : false,
    'values' => array_map('intval', zp_suite_meta_default_values()),
    'currency' => 'PLN',
    'order' => null,
  ];
  if ($cfg['thank'] && function_exists('zp_suite_thank_find_order')) {
    $order = zp_suite_thank_find_order();
    if ($order) { $cfg['order'] = zp_suite_meta_order_value($order); }
  }
  ?>
<script id="zp-suite-827-meta-events">
(function(w,d){
  'use strict';
  if (w.zpMetaEvents827) { return; }
  w.zpMetaEvents827 = 1;
  try { if (w.zpSyntheticAudit === true) { return; } } catch (e) {}

  var C = <?php echo wp_json_encode($cfg); ?>;
  var PIX = String(C.pixel), FN = 'fb' + 'q';
  var Q = [], timer = 0, since = Date.now(), MAXWAIT = 180000, seen = {};
  var path = String(d.location.pathname || '').toLowerCase();

  function marketingOk() {
    try {
      var cb = w.Cookiebot;
      if (cb && cb.consent) { return cb.consent.marketing === true; }
    } catch (e) {}
    return true;
  }
  function pixelReady() {
    var F = w[FN];
    if (typeof F !== 'function' || typeof F.getState !== 'function') { return false; }
    try {
      var px = F.getState().pixels || [];
      for (var i = 0; i < px.length; i++) { if (String(px[i].id) === PIX) { return true; } }
    } catch (e) {}
    return false;
  }
  function store(kind) { try { return w[kind]; } catch (e) { return null; } }
  function alreadyDone(once) {
    if (!once) { return false; }
    var s = store(once.s);
    try { if (s && s.getItem(once.k)) { return true; } } catch (e) {}
    return !!seen['once:' + once.k];
  }
  function markDone(once) {
    if (!once) { return; }
    seen['once:' + once.k] = 1;
    var s = store(once.s);
    try { if (s) { s.setItem(once.k, String(Date.now())); } } catch (e) {}
  }
  function eventId(name) {
    return 'zp-' + name + '-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 8);
  }
  function send(ev) {
    if (alreadyDone(ev.once)) { return; }
    markDone(ev.once);
    try { w[FN]('trackSingle', PIX, ev.name, ev.params || {}, { eventID: ev.id }); } catch (e) {}
  }
  function flush() {
    timer = 0;
    if (!Q.length) { return; }
    if (marketingOk() && pixelReady()) { Q.splice(0, Q.length).forEach(send); return; }
    if (Date.now() - since > MAXWAIT) { Q.length = 0; return; }
    timer = setTimeout(flush, 600);
  }
  function track(name, params, id, once) {
    if (alreadyDone(once)) { return; }
    if (once) {
      if (seen['queued:' + once.k]) { return; }
      seen['queued:' + once.k] = 1;
    }
    Q.push({ name: name, params: params || {}, id: id || eventId(name.toLowerCase()), once: once || null });
    if (!timer) { flush(); }
  }
  w.zpMetaTrack = track;
  w.addEventListener('CookiebotOnAccept', function () { since = Date.now(); if (Q.length && !timer) { flush(); } });

  function amount(text) {
    var m = String(text || '').replace(/ /g, ' ').match(/\d[\d ]*/);
    return m ? (parseInt(m[0].replace(/ /g, ''), 10) || 0) : 0;
  }
  function withValue(params, cat, value) {
    var v = +value || +(C.values || {})[cat] || +(C.values || {})['studio-wyceny'] || 0;
    if (v > 0) { params.value = v; params.currency = C.currency || 'PLN'; }
    return params;
  }

  function serviceFrom(text) {
    var t = String(text || '').toLowerCase();
    if (/sklep|shop|woo/.test(t)) { return 'sklepy'; }
    if (/logo|brand|identyfikac/.test(t)) { return 'logo-branding'; }
    if (/kampani|reklam|ads/.test(t)) { return 'kampanie'; }
    if (/stron|web|www|landing|wordpress/.test(t)) { return 'strony'; }
    return '';
  }

  // ViewContent — service landing pages.
  var lp = path.match(/^\/(?:en\/)?(<?php echo zp_suite_service_slug_regex(); ?>)\/?$/);
  if (lp) { track('ViewContent', { content_category: serviceFrom(lp[1]), content_name: lp[1] }); }

  // Lead — Studio Wyceny thank-you pages (redirect target after a successful submission).
  var ty = path.match(/dziekujemy-za-(zamowienie-logo|zapytanie-o-wycene-strony-internetowej|zapytanie-o-wycene-sklepu-internetowego)/);
  if (C.thank || ty) {
    var order = '';
    try { order = new URLSearchParams(d.location.search).get('zp_order') || ''; } catch (e) {}
    var cat = (C.order && C.order.cat) || (ty ? serviceFrom(ty[1]) : '') || 'studio-wyceny';
    track('Lead', withValue({ content_category: cat, content_name: 'Studio Wyceny' }, cat, C.order && C.order.value),
      order ? ('zp-lead-' + order) : null,
      order ? { s: 'localStorage', k: 'zpMetaLead:' + order } : { s: 'sessionStorage', k: 'zpMetaLead:' + path });
  }

  // InitiateCheckout — first real interaction with the Studio Wyceny configurator.
  function studioStart(e) {
    var t = e.target;
    var root = t && t.closest ? t.closest('#zpbsUltimate') : null;
    if (!root) { return; }
    var svc = { web: 'strony', shop: 'sklepy', brand: 'logo-branding', ads: 'kampanie' }[(root.dataset && root.dataset.service) || ''] || 'studio-wyceny';
    // Logo: price of the selected package. Websites/shops: the client's budget slider.
    var v = 0;
    if (svc === 'logo-branding') {
      var chip = root.querySelector('.is-on .zpbsChip--price, .is-on .zpbsMiniCard__price');
      v = chip ? amount(chip.textContent) : 0;
    } else {
      var range = root.querySelector('[data-budget-range]');
      if (range && !root.querySelector('[data-budget-unset]:checked')) { v = +range.value || 0; }
    }
    if (v < 100) { v = 0; }
    track('InitiateCheckout', withValue({ content_category: svc, content_name: 'Studio Wyceny' }, svc, v), null,
      { s: 'sessionStorage', k: 'zpMetaIC' });
  }
  d.addEventListener('click', studioStart, true);
  d.addEventListener('change', studioStart, true);

  // Contact — phone / e-mail / WhatsApp / Messenger links.
  d.addEventListener('click', function (e) {
    var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
    if (!a) { return; }
    var h = String(a.getAttribute('href') || '').toLowerCase(), ch = '';
    if (h.indexOf('tel:') === 0) { ch = 'telefon'; }
    else if (h.indexOf('mailto:') === 0) { ch = 'email'; }
    else if (/^(?:https?:\/\/)?(?:wa\.me|api\.whatsapp\.com|chat\.whatsapp\.com)\b/.test(h) || h.indexOf('whatsapp:') === 0) { ch = 'whatsapp'; }
    else if (/^(?:https?:\/\/)?(?:m\.me|(?:www\.)?messenger\.com)\b/.test(h)) { ch = 'messenger'; }
    if (!ch || seen['contact:' + ch]) { return; }
    seen['contact:' + ch] = 1;
    track('Contact', { content_category: ch });
  }, true);

  // Lead — successful AJAX submissions of contact forms and Studio Wyceny without redirect.
  var origFetch = w.fetch;
  if (typeof origFetch === 'function') {
    w.fetch = function (input, init) {
      var act = '', svc = '';
      try {
        var b = init && init.body;
        if (b && typeof FormData !== 'undefined' && b instanceof FormData) {
          act = String(b.get('action') || '');
          svc = String(b.get('services') || '');
        }
      } catch (e) {}
      var p = origFetch.apply(this, arguments);
      if (act === 'zp_suite_contact' || act === 'zp_studio_submit') {
        p.then(function (resp) {
          return resp.clone().json().then(function (res) {
            if (!res || !res.success) { return; }
            var dd = res.data || {};
            if (act === 'zp_studio_submit') {
              if (dd.redirect) { return; }
              track('Lead', withValue({ content_category: 'studio-wyceny', content_name: 'Studio Wyceny' }, 'studio-wyceny', 0),
                dd.id ? ('zp-lead-' + dd.id) : null, dd.id ? { s: 'localStorage', k: 'zpMetaLead:' + dd.id } : null);
              return;
            }
            var lc = serviceFrom(svc) || 'kontakt';
            track('Lead', withValue({ content_category: lc, content_name: 'Formularz kontaktowy' }, lc, 0));
          });
        }).catch(function () {});
      }
      return p;
    };
  }
})(window, document);
</script>
  <?php
}, 120);
