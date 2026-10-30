/*! Zaprojektowani Languages — language switch. 1.2.0: the page changes language in place, under a cross-fade
    (zpl-soft.js, loaded when the visitor reaches for the switch); if that is not possible it opens the other
    language's own address with a smooth transition. Three or four languages: a menu in the header bar. */
(function () {
  'use strict';
  var doc = document, html = doc.documentElement;
  var LANGS = { pl: 1, en: 1, de: 1, es: 1 };
  var LABEL = { pl: 'Język', en: 'Language', de: 'Sprache', es: 'Idioma' };
  var NAME = { pl: 'Polski', en: 'English', de: 'Deutsch', es: 'Español' };
  function readCfg() {
    var el = doc.getElementById('zpl-config');
    try { return el ? JSON.parse(el.textContent) || {} : {}; } catch (e) { return {}; }
  }
  var cfg = readCfg();
  var lang = LANGS[cfg.lang] ? cfg.lang : 'pl';
  var rest = typeof cfg.rest === 'string' ? cfg.rest : '';
  window.ZPLSwitch = { lang: lang };

  // The incoming page installs the same guard early in <head>; this document owns the outgoing transition.
  window.addEventListener('pageswap', function (e) {
    if (typeof window.ZPLTrackTransition === 'function') window.ZPLTrackTransition(e);
  });

  function switches() { return doc.querySelectorAll('[data-zpl-switch]'); }
  function each(list, fn) { Array.prototype.forEach.call(list, fn); }
  function closest(el, sel) { return el && el.closest ? el.closest(sel) : null; }

  // ------------------------------------------------------------- theme
  // The header sits transparent over a dark hero on some pages: the switch then uses its light-on-dark look.
  // 3.1.1: the look depends only on three classes of the header, so only its class changes are watched, and an update is
  // scheduled only when they change the look.
  var raf = 0;
  function navDark(nav) {
    return !!(nav && nav.classList.contains('zpNewNav--heroOverlay') && !nav.classList.contains('is-scrolled') && !nav.classList.contains('is-mega-open'));
  }
  function syncTheme() {
    raf = 0;
    each(switches(), function (s) {
      var nav = closest(s, '#zpNewNav,.zpNewNav');
      var dark = !s.classList.contains('zpl-switch--drawer') && navDark(nav);
      if (s.classList.contains('zpl-switch--ondark') !== dark) s.classList.toggle('zpl-switch--ondark', dark);
    });
  }
  function queueTheme() { if (!raf) raf = (window.requestAnimationFrame || setTimeout)(syncTheme); }
  function watchTheme() {
    syncTheme();
    window.addEventListener('load', queueTheme);
    var nav = doc.getElementById('zpNewNav');
    if (nav && window.MutationObserver) {
      var seen = navDark(nav);
      new MutationObserver(function () {
        var now = navDark(nav);
        if (now !== seen) { seen = now; queueTheme(); }
      }).observe(nav, { attributes: true, attributeFilter: ['class'] });
      return;
    }
    window.addEventListener('scroll', queueTheme, { passive: true });
    window.addEventListener('resize', queueTheme, { passive: true });
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
      if (s.classList.contains('zpl-switch--multi')) {
        Object.keys(LANGS).forEach(function (l) { s.classList.toggle('is-' + l, l === to); });
        s.setAttribute('data-zpl-current', to);
        each(s.querySelectorAll('[data-zpl-lang]'), function (a, i) { if (a.getAttribute('data-zpl-lang') === to) s.style.setProperty('--zpl-i', String(i)); });
        var code = s.querySelector('.zpl-switch__now .zpl-switch__code');
        if (code) code.textContent = to.toUpperCase();
        var btn = s.querySelector('.zpl-switch__btn');
        if (btn) btn.setAttribute('aria-label', LABEL[to] + ': ' + NAME[to]);
        var cap = s.querySelector('.zpl-switch__caption');
        if (cap) cap.textContent = LABEL[to];
        s.setAttribute('aria-label', LABEL[to]);
      } else {
        s.classList.toggle('is-en', to === 'en');
        s.classList.toggle('is-pl', to !== 'en');
      }
      if (busy) { s.classList.add('is-busy'); s.setAttribute('data-zpl-to', to); s.setAttribute('aria-busy', 'true'); }
      else { s.classList.remove('is-busy'); s.removeAttribute('data-zpl-to'); s.removeAttribute('aria-busy'); }
      each(s.querySelectorAll('[data-zpl-lang]'), function (a) { a.setAttribute('aria-current', a.getAttribute('data-zpl-lang') === to ? 'true' : 'false'); });
    });
  }

  // ------------------------------------------------------------- menu
  function setOpen(s, open) {
    if (!s) return;
    s.classList.toggle('is-open', open);
    var b = s.querySelector('.zpl-switch__btn');
    if (b) b.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) warm(s);
  }
  function closeMenus(except) {
    each(doc.querySelectorAll('.zpl-switch--menu.is-open'), function (s) { if (s !== except) setOpen(s, false); });
  }
  function items(s) { return Array.prototype.slice.call(s.querySelectorAll('.zpl-switch__menu [data-zpl-lang]')); }

  // ------------------------------------------------------- in place
  var softP = null;
  function soft() {
    if (window.ZPLSoft) return Promise.resolve(window.ZPLSoft);
    if (softP) return softP;
    var s = cfg.soft;
    if (!s || !s.js || !window.fetch || !window.Promise || !window.DOMParser || !window.history || !history.pushState) return Promise.resolve(null);
    softP = new Promise(function (resolve) {
      var need = [];
      if (!window.ZPLCore && s.core) need.push(s.core);
      need.push(s.js);
      var left = need.length;
      need.forEach(function (src) {
        var el = doc.createElement('script');
        el.src = src; el.async = false;
        el.onload = function () { if (!--left) resolve(window.ZPLSoft || null); };
        el.onerror = function () { resolve(null); };
        doc.head.appendChild(el);
      });
    });
    return softP;
  }
  /** The visitor reaches for the switch: the engine and this page's own copy load; an option also fetches its page. */
  function warm(s, a) {
    if (!cfg.soft) return;
    soft().then(function (S) { if (S) S.prepare(a && a.getAttribute('data-zpl-lang') !== lang ? a.href : ''); });
  }

  // ------------------------------------------------------- navigation
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
  function change(a, to) {
    closeMenus();
    show(to, true);
    var href = a.href;
    return soft().then(function (S) { return S ? S.run(to, href) : false; }).then(function (ok) {
      if (!ok) { go(a, to); return; }
      cfg = readCfg();
      lang = to;
      window.ZPLSwitch.lang = to;
      remember(to);
      show(to, false);
      syncTheme();
    }, function () { go(a, to); });
  }

  doc.addEventListener('click', function (e) {
    var btn = closest(e.target, '[data-zpl-switch] .zpl-switch__btn');
    if (btn) {
      e.preventDefault();
      var m = closest(btn, '[data-zpl-switch]');
      closeMenus(m);
      setOpen(m, !m.classList.contains('is-open'));
      return;
    }
    var a = closest(e.target, '[data-zpl-switch] [data-zpl-lang]');
    if (!a) { closeMenus(); return; }
    if (e.defaultPrevented || e.button > 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    var to = a.getAttribute('data-zpl-lang');
    if (!LANGS[to]) return;
    var sw = closest(a, '[data-zpl-switch]');
    if (to === lang) {
      e.preventDefault();
      if (sw && sw.classList.contains('zpl-switch--multi')) { closeMenus(); return; }
      // Tapping the active option of the compact PL/EN chip toggles, which feels natural there.
      if (!sw || !sw.classList.contains('zpl-switch--chip')) return;
      to = lang === 'en' ? 'pl' : 'en';
      a = sw.querySelector('[data-zpl-lang="' + to + '"]');
      if (!a) return;
    }
    e.preventDefault();
    change(a, to);
  }, true);

  doc.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var open = doc.querySelector('.zpl-switch--menu.is-open');
      if (open) { setOpen(open, false); var b = open.querySelector('.zpl-switch__btn'); if (b) b.focus(); }
      return;
    }
    if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
    var s = closest(e.target, '.zpl-switch--menu');
    if (!s) return;
    var list = items(s);
    if (!list.length) return;
    e.preventDefault();
    if (!s.classList.contains('is-open')) setOpen(s, true);
    var i = list.indexOf(doc.activeElement);
    i = e.key === 'ArrowDown' ? (i + 1) % list.length : (i <= 0 ? list.length - 1 : i - 1);
    list[i].focus();
  });
  doc.addEventListener('focusout', function (e) {
    var s = closest(e.target, '.zpl-switch--menu.is-open');
    if (s && !(e.relatedTarget && s.contains(e.relatedTarget))) setTimeout(function () { if (!s.contains(doc.activeElement)) setOpen(s, false); }, 0);
  });

  // Back to this page from the other language (back/forward cache): the switch shows this page's language again.
  window.addEventListener('pageshow', function (e) {
    if (!e.persisted) return;
    show(lang, false);
    var st = doc.getElementById('zpl-vt-css');
    if (st) st.parentNode.removeChild(st);
  });
  // After Back/Forward between language versions changed in place, the switch follows the page.
  doc.addEventListener('zpl:change', function (e) {
    var d = (e && e.detail) || {};
    if (!d.soft || !LANGS[d.lang]) return;
    cfg = readCfg();
    lang = d.lang;
    window.ZPLSwitch.lang = d.lang;
    show(d.lang, false);
    syncTheme();
  });

  // ---------------------------------------------------------- fetch ahead
  var intent = function (e) {
    if (e.type === 'pointerover' && e.pointerType && e.pointerType !== 'mouse') return;
    var s = closest(e.target, '[data-zpl-switch]');
    if (!s) return;
    var a = closest(e.target, '[data-zpl-lang]');
    warm(s, a);
  };
  if (cfg.soft) {
    doc.addEventListener('pointerover', intent, { passive: true });
    doc.addEventListener('focusin', intent);
    doc.addEventListener('touchstart', intent, { passive: true });
  } else {
    // Without the in-place change: the other language is fetched when the pointer reaches the switch, so it opens at once.
    var sel = '[data-zpl-switch] a[data-zpl-lang]:not([aria-current="true"])';
    var rules = false;
    try { rules = !!(window.HTMLScriptElement && HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')); } catch (e) {}
    if (rules) {
      var sr = doc.createElement('script');
      sr.type = 'speculationrules';
      sr.textContent = JSON.stringify({ prefetch: [{ where: { selector_matches: sel }, eagerness: 'moderate' }] });
      doc.head.appendChild(sr);
    }
  }

  function boot() { try { watchTheme(); } catch (e) {} }
  if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', boot); else boot();
}());
