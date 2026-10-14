/*! Zaprojektowani Languages — PL/EN switch: opens the other language's own address with a smooth transition. */
(function () {
  'use strict';
  var doc = document, html = doc.documentElement;
  var cfgEl = doc.getElementById('zpl-config');
  var cfg = {};
  try { cfg = cfgEl ? JSON.parse(cfgEl.textContent) || {} : {}; } catch (e) { cfg = {}; }
  var lang = cfg.lang === 'en' ? 'en' : 'pl';
  var rest = typeof cfg.rest === 'string' ? cfg.rest : '';
  window.ZPLSwitch = { lang: lang };

  function switches() { return doc.querySelectorAll('[data-zpl-switch]'); }
  function each(list, fn) { Array.prototype.forEach.call(list, fn); }

  // ------------------------------------------------------------- theme
  // The header sits transparent over a dark hero on some pages: the switch then uses its light-on-dark look.
  var raf = 0;
  function syncTheme() {
    raf = 0;
    each(switches(), function (s) {
      var nav = s.closest ? s.closest('#zpNewNav,.zpNewNav') : null;
      var dark = !!(nav && !s.classList.contains('zpl-switch--drawer') && nav.classList.contains('zpNewNav--heroOverlay') &&
        !nav.classList.contains('is-scrolled') && !nav.classList.contains('is-mega-open'));
      s.classList.toggle('zpl-switch--ondark', dark);
    });
    html.classList.add('zpl-theme');
  }
  function queueTheme() { if (!raf) raf = (window.requestAnimationFrame || setTimeout)(syncTheme); }
  function watchTheme() {
    syncTheme();
    var later = function () { queueTheme(); setTimeout(queueTheme, 220); setTimeout(queueTheme, 600); };
    window.addEventListener('scroll', queueTheme, { passive: true });
    window.addEventListener('resize', later, { passive: true });
    window.addEventListener('load', later);
    var nav = doc.getElementById('zpNewNav');
    if (nav && window.MutationObserver) {
      new MutationObserver(later).observe(nav, { attributes: true, attributeFilter: ['class', 'style'] });
      var shell = nav.querySelector('.zpNewNav__shell');
      if (shell) new MutationObserver(later).observe(shell, { attributes: true, attributeFilter: ['class', 'style'] });
    }
    later();
  }

  // ------------------------------------------------------------ choice
  function remember(to) {
    var secure = location.protocol === 'https:' ? ';Secure' : '';
    try {
      doc.cookie = 'zpl_lang_pref=' + to + ';path=/;max-age=31536000;SameSite=Lax' + secure;
      doc.cookie = 'zpl_lang_session=' + to + ';path=/;SameSite=Lax' + secure;
    } catch (e) {}
    // The server sets the year-long cookie again: Safari keeps cookies written by scripts for 7 days only.
    if (rest && window.fetch) {
      try {
        fetch(rest + 'pref', { method: 'POST', credentials: 'same-origin', keepalive: true, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ lang: to }) }).catch(function () {});
      } catch (e) {}
    }
  }

  function show(to, busy) {
    each(switches(), function (s) {
      s.classList.toggle('is-en', to === 'en');
      s.classList.toggle('is-pl', to !== 'en');
      if (busy) { s.classList.add('is-busy'); s.setAttribute('data-zpl-to', to); s.setAttribute('aria-busy', 'true'); }
      else { s.classList.remove('is-busy'); s.removeAttribute('data-zpl-to'); s.removeAttribute('aria-busy'); }
      each(s.querySelectorAll('[data-zpl-lang]'), function (a) { a.setAttribute('aria-current', a.getAttribute('data-zpl-lang') === to ? 'true' : 'false'); });
    });
  }

  function smooth() {
    try {
      if (!window.CSS || !CSS.supports || !CSS.supports('view-transition-name: none')) return;
      if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      if (doc.getElementById('zpl-vt-css')) return;
      var st = doc.createElement('style');
      st.id = 'zpl-vt-css';
      st.textContent = '@view-transition{navigation:auto}::view-transition-group(root){animation-duration:.3s}';
      doc.head.appendChild(st);
    } catch (e) {}
  }

  function go(a, to) {
    var url;
    try { url = new URL(a.getAttribute('href'), location.href); } catch (e) { return false; }
    if (url.origin !== location.origin) return false;
    url.search = location.search;
    url.hash = location.hash;
    var max = Math.max(0, html.scrollHeight - window.innerHeight);
    try {
      sessionStorage.setItem('zpl_vt', JSON.stringify({ t: Date.now(), to: to, src: window.ZPLSourcePath || '', y: Math.round(window.scrollY || 0), r: max > 0 ? (window.scrollY || 0) / max : 0 }));
    } catch (e) {}
    remember(to);
    show(to, true);
    smooth();
    location.assign(url.href);
    return true;
  }

  doc.addEventListener('click', function (e) {
    var a = e.target && e.target.closest ? e.target.closest('[data-zpl-switch] [data-zpl-lang]') : null;
    if (!a || e.defaultPrevented || e.button > 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    var to = a.getAttribute('data-zpl-lang') === 'en' ? 'en' : 'pl';
    var sw = a.closest('[data-zpl-switch]');
    if (to === lang) {
      e.preventDefault();
      // Tapping the active option of the compact chip toggles, which feels natural there.
      if (!sw || !sw.classList.contains('zpl-switch--chip')) return;
      to = lang === 'en' ? 'pl' : 'en';
      a = sw.querySelector('[data-zpl-lang="' + to + '"]');
      if (!a) return;
    }
    if (go(a, to)) e.preventDefault();
  }, true);

  // Back to this page from the other language (back/forward cache): the switch shows this page's language again.
  window.addEventListener('pageshow', function (e) {
    if (!e.persisted) return;
    show(lang, false);
    var st = doc.getElementById('zpl-vt-css');
    if (st) st.parentNode.removeChild(st);
  });

  // ---------------------------------------------------------- fetch ahead
  // The other language is fetched when the pointer reaches the switch (Chromium: also on touch, without a second
  // download), so it opens at once.
  var sel = '[data-zpl-switch] a[data-zpl-lang="' + (lang === 'en' ? 'pl' : 'en') + '"]';
  var rules = false;
  try { rules = !!(window.HTMLScriptElement && HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')); } catch (e) {}
  if (rules) {
    var sr = doc.createElement('script');
    sr.type = 'speculationrules';
    sr.textContent = JSON.stringify({ prefetch: [{ where: { selector_matches: sel }, eagerness: 'moderate' }] });
    doc.head.appendChild(sr);
  } else {
    // Mouse and keyboard only: a tap is followed by the click at once, and a second download would only compete.
    var done = {};
    var warm = function (e) {
      if (e.type === 'pointerover' && e.pointerType && e.pointerType !== 'mouse') return;
      var s = e.target && e.target.closest ? e.target.closest('[data-zpl-switch]') : null;
      var a = s ? s.querySelector('a[data-zpl-lang="' + (lang === 'en' ? 'pl' : 'en') + '"]') : null;
      var href = a ? a.href : '';
      if (!href || done[href]) return;
      done[href] = 1;
      var l = doc.createElement('link');
      l.rel = 'prefetch';
      l.href = href;
      doc.head.appendChild(l);
    };
    doc.addEventListener('pointerover', warm, { passive: true });
    doc.addEventListener('focusin', warm);
  }

  function boot() { try { watchTheme(); } catch (e) {} }
  if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', boot); else boot();
}());
