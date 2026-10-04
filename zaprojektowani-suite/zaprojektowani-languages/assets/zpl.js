/*! Zaprojektowani Languages — PL/EN switch without reload. */
(function () {
  'use strict';
  var C = window.ZPLCore;
  var cfgEl = document.getElementById('zpl-config');
  if (!C || !cfgEl || !window.fetch || !window.WeakMap) return;
  var cfg;
  try { cfg = JSON.parse(cfgEl.textContent); } catch (e) { return; }

  if (cfg.attrs && C.addAttrs) C.addAttrs(cfg.attrs); // attributes added by Skaner EN fixes
  var doc = document, html = doc.documentElement;
  var state = { lang: cfg.lang === 'en' ? 'en' : 'pl', busy: false };
  var dicts = { en: null, pl: null };
  var pending = { en: null, pl: null };
  var routes = cfg.routes || null, reverse = null;
  var mem = new WeakMap(), attrMem = new WeakMap(), hrefMem = new WeakMap(), langMem = new WeakMap();
  var known = { en: new Set(), pl: new Set() };
  var negative = { en: new Set(), pl: new Set() };
  var reported = new Set();
  var inflight = 0; // server lookups in progress (ZPL.whenIdle)
  var titleMem = null;
  var applying = false;
  var NEVER = /^\/(?:wp-admin|wp-login\.php|wp-json|wp-content|wp-includes|wp-cron\.php|xmlrpc\.php|feed|comments\/feed|sitemap)(?:\/|$)/i;
  var INLINE = { a: 1, abbr: 1, b: 1, bdi: 1, bdo: 1, br: 1, cite: 1, code: 1, data: 1, del: 1, dfn: 1, em: 1, font: 1, i: 1, img: 1, ins: 1, kbd: 1, mark: 1, q: 1, s: 1, samp: 1, small: 1, span: 1, strong: 1, sub: 1, sup: 1, time: 1, u: 1, 'var': 1, wbr: 1, svg: 1, picture: 1, input: 1 };
  var POLISH = /[ąćęłńóśźżĄĆĘŁŃÓŚŹŻ]|\b(?:i|w|z|na|do|się|jest|nie|dla|oraz|lub|że|jak|czy|od|po|przez|są|nas)\b/;

  var store = {
    get: function (k) { try { return window.sessionStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { window.sessionStorage.setItem(k, v); } catch (e) {} }
  };

  function setLangPreference(lang) {
    try {
      document.cookie = 'zpl_lang_pref=' + (lang === 'en' ? 'en' : 'pl') + ';path=/;max-age=2592000;SameSite=Lax' + (location.protocol === 'https:' ? ';Secure' : '');
    } catch (e) {}
  }

  // ------------------------------------------------------------------ data

  function getJSON(url) {
    return fetch(url, { credentials: 'omit' }).then(function (r) { return r.ok ? r.json() : {}; }).catch(function () { return {}; });
  }
  function loadDict(lang) {
    if (dicts[lang]) return Promise.resolve(dicts[lang]);
    if (pending[lang]) return pending[lang];
    var jobs = [];
    jobs.push(cfg.page ? getJSON(cfg.data + lang + '/' + cfg.page + '.json?v=' + encodeURIComponent(cfg.v)) : Promise.resolve({}));
    jobs.push(cfg.ov ? getJSON(cfg.rest + 'overrides?lang=' + lang + '&v=' + encodeURIComponent(cfg.ov)) : Promise.resolve({}));
    jobs.push(routes ? Promise.resolve(null) : getJSON(cfg.data + 'routes.json?v=' + encodeURIComponent(cfg.v)));
    jobs.push(getJSON(cfg.data + lang + '/dyn.json?v=' + encodeURIComponent(cfg.v)));
    pending[lang] = Promise.all(jobs).then(function (res) {
      var m = new Map(), page = res[0] || {}, ov = (res[1] && res[1].map) || {}, dyn = res[3] || {};
      Object.keys(dyn).forEach(function (k) { m.set(k, dyn[k]); });
      Object.keys(page).forEach(function (k) { m.set(k, page[k]); });
      Object.keys(ov).forEach(function (k) { m.set(k, ov[k]); });
      if (!routes && res[2] && res[2].routes) routes = res[2].routes;
      m.forEach(function (v) { known[lang].add(C.norm(stripMarkers(v))); });
      dicts[lang] = m;
      pending[lang] = null;
      return m;
    });
    return pending[lang];
  }
  // The server answers at most 400 keys per request; long pages (e.g. service pages without a page
  // dictionary) ask in parallel batches, so no text is left out of the switch.
  function lookup(lang, keys) {
    keys = keys.filter(function (k) { return !negative[lang].has(k); });
    if (keys.length <= 400) return lookupBatch(lang, keys);
    var jobs = [];
    for (var i = 0; i < keys.length && i < 2400; i += 400) jobs.push(lookupBatch(lang, keys.slice(i, i + 400)));
    return Promise.all(jobs).then(function (maps) { return Object.assign.apply(Object, [{}].concat(maps)); });
  }
  function lookupBatch(lang, keys) {
    if (!keys.length) return Promise.resolve({});
    var ctl = window.AbortController ? new AbortController() : null;
    var timer = ctl ? setTimeout(function () { ctl.abort(); }, 10000) : 0;
    inflight++;
    return fetch(cfg.rest + 'lookup', {
      method: 'POST', credentials: 'omit', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ lang: lang, src: cfg.src, keys: keys }), signal: ctl ? ctl.signal : undefined
    }).then(function (r) { return r.ok ? r.json() : null; }).catch(function () { return null; }).then(function (j) {
      inflight--;
      if (timer) clearTimeout(timer);
      // A failed/slow request says nothing about the keys: only a real answer may mark them as untranslated.
      if (!j || typeof j.map !== 'object' || j.map === null) return {};
      var map = j.map, d = dicts[lang] || (dicts[lang] = new Map());
      keys.forEach(function (k) {
        if (Object.prototype.hasOwnProperty.call(map, k)) { d.set(k, map[k]); known[lang].add(C.norm(stripMarkers(map[k]))); }
        else negative[lang].add(k);
      });
      return map;
    });
  }
  function report(keys) {
    var list = keys.filter(function (k) { return !reported.has(k) && POLISH.test(k); }).slice(0, 60);
    if (!list.length || state.lang !== 'en') return;
    list.forEach(function (k) { reported.add(k); });
    try {
      fetch(cfg.rest + 'missing', { method: 'POST', credentials: 'omit', keepalive: true, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ lang: 'en', src: cfg.src, keys: list }) }).catch(function () {});
    } catch (e) {}
  }
  function stripMarkers(s) { return String(s).replace(/<\/?\d+\/?>/g, ' '); }

  function translation(key, lang) {
    var d = dicts[lang];
    if (!d) return undefined;
    var t = d.get(key);
    if (t !== undefined) return t;
    var tpl = C.template(key);
    if (tpl) { var x = d.get(tpl[0]); if (x !== undefined) return C.fill(x, tpl[1], lang); }
    return undefined;
  }

  // ------------------------------------------------------------------ routes

  function normPath(p) {
    try { p = decodeURI(p); } catch (e) {}
    p = ('/' + p.replace(/^\/+/, '')).replace(/\/{2,}/g, '/');
    if (p !== '/' && p.slice(-1) !== '/' && !/\.[a-z0-9]{2,5}$/i.test(p)) p += '/';
    return p;
  }
  function excluded(p) { return (cfg.ex || []).some(function (x) { return p.indexOf(x) === 0 || p + '/' === x; }); }
  function rev() {
    if (!reverse && routes) { reverse = {}; Object.keys(routes).forEach(function (k) { reverse[routes[k]] = k; }); }
    return reverse || {};
  }
  function enPath(p) {
    if (routes && routes[p]) return routes[p];
    var m = /^(.*\/)page\/(\d+)\/$/.exec(p);
    if (m && routes && routes[m[1]]) return routes[m[1]] + 'page/' + m[2] + '/';
    return '/en' + p;
  }
  function plPath(p) {
    var r = rev();
    if (r[p]) return r[p];
    var m = /^(.*\/)page\/(\d+)\/$/.exec(p);
    if (m && r[m[1]]) return r[m[1]] + 'page/' + m[2] + '/';
    return p === '/en/' ? '/' : p.slice(3);
  }
  function localize(href, lang) {
    if (!href) return href;
    var s = String(href).trim();
    if (!s || s.charAt(0) === '#' || /^(?:mailto|tel|sms|javascript|data|whatsapp|viber):/i.test(s)) return href;
    var u;
    try { u = new URL(s, location.href); } catch (e) { return href; }
    if (!/^https?:$/.test(u.protocol)) return href;
    if (u.host.replace(/^www\./, '') !== location.host.replace(/^www\./, '')) return href;
    var base = cfg.base || '', p = u.pathname;
    if (base && p.indexOf(base) === 0) p = p.slice(base.length) || '/';
    if (NEVER.test(p) || (/\.[a-z0-9]{2,5}$/i.test(p) && !/\.html?$/i.test(p))) return href;
    p = normPath(p);
    var isEn = p === '/en/' || p.indexOf('/en/') === 0, np;
    if (lang === 'en') { if (isEn || excluded(p)) return href; np = enPath(p); }
    else { if (!isEn) return href; np = plPath(p); }
    var out = base + np + u.search + u.hash;
    if (/^[a-z][a-z0-9+.-]*:\/\//i.test(s) || s.indexOf('//') === 0) out = u.protocol + '//' + u.host + out;
    return out;
  }

  // ------------------------------------------------------------------ apply

  function build(toks, st, until, els) {
    var frag = doc.createDocumentFragment();
    while (st.i < toks.length) {
      var t = toks[st.i++];
      if (t[0] === 'x') { frag.appendChild(doc.createTextNode(t[1])); continue; }
      if (t[0] === 'c') { if (t[1] === until) return frag; continue; }
      var el = els[t[1]];
      if (!el) continue;
      if (t[0] === 'a') { frag.appendChild(el); continue; }
      var inner = build(toks, st, t[1], els);
      while (el.firstChild) el.removeChild(el.firstChild);
      el.appendChild(inner);
      frag.appendChild(el);
    }
    return frag;
  }
  function rebuild(rootEl, toks, els) {
    var f = rootEl.firstChild, l = rootEl.lastChild;
    var lead = f && f.nodeType === 3 ? (f.nodeValue.match(/^\s*/) || [''])[0] : '';
    var trail = l && l.nodeType === 3 ? (l.nodeValue.match(/\s*$/) || [''])[0] : '';
    var frag = build(toks, { i: 0 }, 0, els);
    while (rootEl.firstChild) rootEl.removeChild(rootEl.firstChild);
    if (lead) rootEl.appendChild(doc.createTextNode(lead));
    rootEl.appendChild(frag);
    if (trail) rootEl.appendChild(doc.createTextNode(trail));
  }
  function segAfter(rootEl) {
    var r = null;
    C.walk(rootEl, function (s) { if (!r && s.type === 'multi' && s.root === rootEl) r = s; });
    return r;
  }
  /** Two-state memory {a, b}: returns the state to restore when the node still shows the other one. */
  function pick(m, key, to) {
    if (!m) return null;
    var cur = m.a.lang === state.lang ? m.a : (m.b.lang === state.lang ? m.b : null);
    var tgt = m.a.lang === to ? m.a : (m.b.lang === to ? m.b : null);
    return cur && tgt && cur !== tgt && cur.k === key ? tgt : null;
  }
  /** Returns true when the segment was changed. */
  function applySegment(seg, to) {
    var node = seg.type === 'multi' ? seg.root : seg.node;
    var back = pick(mem.get(node), seg.k, to);
    if (back) {
      if (seg.type === 'multi') rebuild(node, C.tokens(back.k), back.els);
      else node.nodeValue = back.raw;
      return true;
    }
    var t = translation(seg.k, to);
    if (t === undefined || t === seg.k) return false;
    if (seg.type === 'multi') {
      var toks = C.tokens(t);
      if (!toks || !C.markersMatch(seg.k, t)) return false;
      rebuild(node, toks, seg.els);
      var after = segAfter(node);
      mem.set(node, { a: { lang: state.lang, k: seg.k, els: seg.els }, b: { lang: to, k: after ? after.k : '\u0000', els: after ? after.els : {} } });
    } else {
      var raw = node.nodeValue;
      var lead = (raw.match(/^\s*/) || [''])[0], trail = (raw.match(/\s*$/) || [''])[0];
      node.nodeValue = lead + t + trail;
      mem.set(node, { a: { lang: state.lang, k: seg.k, raw: raw }, b: { lang: to, k: C.norm(t), raw: node.nodeValue } });
    }
    return true;
  }
  function applyAttr(el, name, value, to) {
    var bag = attrMem.get(el) || {};
    var back = pick(bag[name], value, to);
    if (back) { el.setAttribute(name, back.raw); return true; }
    var t = translation(value, to);
    if (t === undefined || t === value) return false;
    var raw = el.getAttribute(name);
    el.setAttribute(name, t);
    bag[name] = { a: { lang: state.lang, k: value, raw: raw }, b: { lang: to, k: C.norm(t), raw: t } };
    attrMem.set(el, bag);
    return true;
  }
  function inSwitch(el) { return el.closest && el.closest('[data-zpl-switch]'); }
  function applyLinks(rootEl, to) {
    var links = rootEl.querySelectorAll ? rootEl.querySelectorAll('a[href],area[href],form[action]') : [];
    var list = Array.prototype.slice.call(links);
    if (rootEl.matches && rootEl.matches('a[href],area[href],form[action]')) list.unshift(rootEl);
    list.forEach(function (el) {
      if (inSwitch(el)) return;
      var attr = el.tagName === 'FORM' ? 'action' : 'href';
      if (attr === 'action' && (el.getAttribute('method') || 'get').toLowerCase() !== 'get') return;
      var cur = el.getAttribute(attr);
      var back = pick(hrefMem.get(el), cur, to);
      if (back) { el.setAttribute(attr, back.k); return; }
      var n = localize(cur, to);
      if (n !== cur) { el.setAttribute(attr, n); hrefMem.set(el, { a: { lang: state.lang, k: cur }, b: { lang: to, k: n } }); }
    });
  }
  /** Suite HTML documents in iframes (Quote Studio chooser): English copy is served via ?zpl_frame=. */
  var RX_FRAME = /\/wp-content\/plugins\/zaprojektowani-suite\/assets\/([A-Za-z0-9_-]+(?:\/[A-Za-z0-9._-]+)*\.html)(?:\?([^#]*))?/;
  function applyFrames(rootEl, to) {
    var list = rootEl.querySelectorAll ? rootEl.querySelectorAll('iframe[src]') : [];
    Array.prototype.forEach.call(list, function (f) {
      var cur = f.getAttribute('src') || '', pl = f.getAttribute('data-zpl-src-pl');
      if (to === 'pl') { if (pl && cur !== pl) f.setAttribute('src', pl); return; }
      if (pl && cur.indexOf('zpl_frame=') >= 0) return;
      var m = cur.match(RX_FRAME);
      if (!m || m[1].indexOf('..') >= 0 || !cfg.frame) return;
      var v = (m[2] || '').match(/(?:^|&)v=([A-Za-z0-9._-]+)/);
      f.setAttribute('data-zpl-src-pl', cur);
      f.setAttribute('src', cfg.frame + (cfg.frame.indexOf('?') >= 0 ? '&' : '?') + 'zpl_frame=' + encodeURIComponent(m[1]).replace(/%2F/g, '/') + (v ? '&v=' + v[1] : ''));
    });
  }
  /** Suite CMS data read by page scripts (window.zp… = [...]): keep it in the page language so later renders match. */
  var blobs = {};
  function snap(v) { return JSON.parse(JSON.stringify(v)); }
  function mapStrings(v, fn) {
    if (typeof v === 'string') return fn(v);
    if (Array.isArray(v)) return v.map(function (x) { return mapStrings(x, fn); });
    if (v && typeof v === 'object') { var o = {}; Object.keys(v).forEach(function (k) { o[k] = mapStrings(v[k], fn); }); return o; }
    return v;
  }
  function assignDeep(t, src) {
    if (!t || !src || typeof t !== 'object' || typeof src !== 'object') return;
    Object.keys(t).forEach(function (k) {
      if (!Object.prototype.hasOwnProperty.call(src, k)) return;
      if (t[k] && typeof t[k] === 'object') assignDeep(t[k], src[k]);
      else if (typeof src[k] !== 'object' && t[k] !== src[k]) t[k] = src[k];
    });
  }
  function dataString(s) {
    if (!s || /^(?:https?:|\/|#|data:|mailto:|tel:)/i.test(s)) return s;
    var k = C.norm(s);
    if (!k || !C.human(k)) return s;
    var t = translation(k, 'en');
    return t === undefined ? s : t;
  }
  function applyData(to) {
    (cfg.blobs || []).forEach(function (name) {
      var live = window[name];
      if (!live || typeof live !== 'object') return;
      var b = blobs[name] || (blobs[name] = {});
      if (!b[state.lang]) b[state.lang] = snap(live);
      if (!b.pl && window.zplData && window.zplData[name]) b.pl = window.zplData[name];
      if (to === 'en' && !b.en && b.pl) b.en = mapStrings(b.pl, dataString);
      if (b[to]) assignDeep(live, b[to]);
    });
  }
  /** Labels inside SVG graphics (<text>/<tspan> without child elements), e.g. chart captions. */
  function applySvg(rootEl, to) {
    var list = rootEl.querySelectorAll ? rootEl.querySelectorAll('svg text, svg tspan') : [];
    Array.prototype.forEach.call(list, function (el) {
      if (el.children.length) return;
      var k = C.norm(el.textContent || '');
      if (!k || !C.human(k)) return;
      var t = translation(k, to);
      if (t !== undefined && t !== k && t.indexOf('<') < 0) el.textContent = t;
    });
  }
  function applyLangAttrs(rootEl, to) {
    var list = rootEl.querySelectorAll ? rootEl.querySelectorAll('[lang]') : [];
    Array.prototype.forEach.call(list, function (el) {
      if (inSwitch(el)) return;
      var v = el.getAttribute('lang') || '';
      if (to === 'en' && /^pl\b/i.test(v)) { langMem.set(el, v); el.setAttribute('lang', 'en'); }
      else if (to === 'pl' && /^en\b/i.test(v)) { el.setAttribute('lang', langMem.get(el) || 'pl'); }
    });
  }

  /** Collect segments/attributes of a subtree. */
  function collect(rootEl) {
    var segs = [], attrs = [];
    C.walk(rootEl, function (s) { segs.push(s); }, function (el) {
      if (!el.hasAttribute) return;
      var items = C.attrItems(el);
      for (var i = 0; i < items.length; i++) attrs.push([el, items[i][0], items[i][1]]);
    });
    return { segs: segs, attrs: attrs };
  }
  function headItems() {
    var attrs = [];
    Array.prototype.forEach.call(doc.head ? doc.head.querySelectorAll('meta') : [], function (m) {
      C.attrItems(m).forEach(function (a) { attrs.push([m, a[0], a[1]]); });
    });
    return attrs;
  }
  function unresolved(col, to) {
    var miss = [];
    col.segs.forEach(function (s) {
      var node = s.type === 'multi' ? s.root : s.node;
      if (pick(mem.get(node), s.k, to)) return;
      if (translation(s.k, to) === undefined && !known[to].has(C.norm(stripMarkers(s.k)))) miss.push(s.k);
    });
    col.attrs.forEach(function (a) {
      if (pick((attrMem.get(a[0]) || {})[a[1]], a[2], to)) return;
      if (translation(a[2], to) === undefined && !known[to].has(a[2])) miss.push(a[2]);
    });
    return miss.filter(function (k, i, arr) { return arr.indexOf(k) === i; });
  }
  function applyCollected(col, to) {
    var changed = 0, missing = [];
    col.segs.forEach(function (s) {
      if (!s.node && !s.root) return;
      if (s.type === 'multi' ? !s.root.isConnected : !s.node.isConnected) return;
      if (applySegment(s, to)) changed++;
      else if (!known[to].has(C.norm(stripMarkers(s.k)))) missing.push(s);
    });
    col.attrs.forEach(function (a) { if (applyAttr(a[0], a[1], a[2], to)) changed++; });
    if (cfg.debug) debugMark(missing, to);
    return missing;
  }
  function debugMark(missing, to) {
    Array.prototype.forEach.call(doc.querySelectorAll('.zpl-debug-miss'), function (el) { el.classList.remove('zpl-debug-miss'); });
    if (to !== 'en') return;
    missing.forEach(function (s) {
      var el = s.type === 'multi' ? s.root : s.node.parentNode;
      if (el && el.classList) el.classList.add('zpl-debug-miss');
    });
    if (window.console) console.info('[zpl] untranslated:', missing.map(function (s) { return s.k; }));
  }
  function withoutObserver(fn) {
    applying = true;
    try { fn(); } finally { if (observer) observer.takeRecords(); applying = false; }
  }

  function targetUrl(to) {
    var path = to === 'en' ? (cfg.en || enPath(cfg.src)) : cfg.src;
    return (cfg.base || '') + path + location.search + location.hash;
  }

  function setTitle(to) {
    var cur = C.norm(doc.title);
    var back = pick(titleMem, cur, to);
    if (back) { doc.title = back.raw; return; }
    var t = translation(cur, to);
    if (t !== undefined) { titleMem = { a: { lang: state.lang, k: cur, raw: doc.title }, b: { lang: to, k: C.norm(t), raw: t } }; doc.title = t; }
  }

  // ------------------------------------------------------------------ switch

  function switchTo(to, opts) {
    opts = opts || {};
    if (to !== 'en' && to !== 'pl') return Promise.resolve(false);
    if (to === state.lang || state.busy) return Promise.resolve(false);
    state.busy = true;
    setBusy(true, to);
    var col, miss = [], early = false;
    return loadDict(to).then(function () {
      col = collect(doc.body);
      col.attrs = col.attrs.concat(headItems());
      miss = unresolved(col, to);
      // Wait briefly for strings that need the server so the page flips in one go; slow answers are applied later.
      if (!miss.length) return col;
      var late = lookup(to, miss).then(function () {
        if (state.lang !== to) { early = true; return; }
        var left;
        withoutObserver(function () { left = applyCollected(col, to); setTitle(to); });
        if (to === 'en') report(left.map(function (s) { return s.k; }));
      });
      return Promise.race([late, new Promise(function (r) { setTimeout(r, 1200); })]).then(function () { return col; });
    }).then(function (col) {
      var missing;
      withoutObserver(function () {
        missing = applyCollected(col, to);
        setTitle(to);
        try { applyData(to); } catch (e) {}
        applyLinks(doc.body, to);
        applyFrames(doc.body, to);
        applyLangAttrs(doc.body, to);
        applySvg(doc.body, to);
        html.setAttribute('lang', to === 'en' ? 'en' : 'pl-PL');
      });
      var from = state.lang;
      state.lang = to;
      cfg.lang = to;
      try { history.replaceState(history.state, '', targetUrl(to)); } catch (e) {}
      if (!opts.auto) { store.set('zpl_lang', to); setLangPreference(to); }
      updateSwitches();
      if (to === 'en') { startObserver(); if (!miss.length || early) report(missing.map(function (s) { return s.k; })); } else if (servedEn) startObserver(); else stopObserver();
      try { doc.dispatchEvent(new CustomEvent('zpl:change', { detail: { lang: to, from: from } })); } catch (e) {}
      return true;
    }).catch(function (e) { if (window.console) console.warn('[zpl]', e); return false; }).then(function (r) {
      state.busy = false;
      setBusy(false);
      return r;
    });
  }

  // ---------------------------------------------------------------- observer

  var observer = null, queue = [], timer = 0, firstQueued = 0;
  function blockOf(node) {
    var el = node.nodeType === 1 ? node : node.parentNode;
    while (el && el.nodeType === 1 && INLINE[C.tagName(el)] && el.parentNode && el !== doc.body) el = el.parentNode;
    return el && el.nodeType === 1 ? el : null;
  }
  function skippedAncestor(el) {
    for (var p = el; p && p !== doc.body; p = p.parentNode) { if (p.nodeType === 1 && C.skipped(p)) return true; }
    return false;
  }
  // Pages served in English keep English data in page scripts (e.g. project modals): after switching to Polish,
  // new renders from that data are translated back, so the observer runs in both directions there.
  var servedEn = cfg.lang === 'en';
  function onMutations(records) {
    if (applying || (state.lang !== 'en' && !servedEn)) return;
    for (var i = 0; i < records.length; i++) {
      var r = records[i];
      if (r.type === 'childList') { for (var j = 0; j < r.addedNodes.length; j++) queue.push(r.addedNodes[j]); }
      else queue.push(r.target);
    }
    if (!queue.length) return;
    var now = Date.now();
    if (!firstQueued) firstQueued = now;
    clearTimeout(timer);
    timer = setTimeout(flushQueue, now - firstQueued > 300 ? 0 : 60);
  }
  function flushQueue() {
    firstQueued = 0; timer = 0;
    var blocks = [], seen = new Set();
    queue.splice(0).forEach(function (n) {
      if (!n || !n.isConnected) return;
      if (n.nodeType === 1 && inSwitch(n)) return;
      var b = blockOf(n);
      if (!b || seen.has(b) || skippedAncestor(b)) return;
      seen.add(b);
      blocks.push(b);
    });
    // Keep only outermost blocks.
    blocks = blocks.filter(function (b) { return !blocks.some(function (o) { return o !== b && o.contains(b); }); });
    if (!blocks.length) return;
    var L = state.lang;
    loadDict(L).then(function () {
      var cols = blocks.map(function (b) { return { b: b, col: b === doc.body ? collect(b) : collectBlock(b) }; });
      var miss = [];
      cols.forEach(function (x) { miss = miss.concat(unresolved(x.col, L)); });
      miss = miss.filter(function (k, i, a) { return a.indexOf(k) === i && !negative[L].has(k); });
      var run = function (final) {
        if (state.lang !== L) return;
        var left = [];
        withoutObserver(function () {
          cols.forEach(function (x) {
            left = left.concat(applyCollected(x.col, L));
            applyLinks(x.b, L);
            applyFrames(x.b, L);
            applyLangAttrs(x.b, L);
          });
        });
        if (final && L === 'en') report(left.map(function (s) { return s.k; }));
      };
      // Known strings go in right away; strings that need the server follow when it answers.
      // Back to Polish only uses the local reverse dictionary (no server round-trips for Polish text).
      if (L !== 'en') miss = [];
      run(!miss.length);
      if (miss.length) lookup(L, miss).then(function () { run(true); });
    });
  }
  function collectBlock(b) {
    // Walk the block the same way the page walk reaches it.
    var col = { segs: [], attrs: [] };
    C.walk(b, function (s) { col.segs.push(s); }, function (el) {
      if (!el.hasAttribute) return;
      C.attrItems(el).forEach(function (a) { col.attrs.push([el, a[0], a[1]]); });
    });
    return col;
  }
  function startObserver() {
    if (observer || !window.MutationObserver) return;
    observer = new MutationObserver(onMutations);
    observer.observe(doc.body, { childList: true, subtree: true, characterData: true, attributes: true, attributeFilter: ['alt', 'title', 'placeholder', 'aria-label', 'value', 'href'].concat(cfg.attrs || []) });
  }
  function stopObserver() { if (observer) { observer.disconnect(); observer = null; } queue.length = 0; }

  // ---------------------------------------------------------------------- UI

  function switches() { return doc.querySelectorAll('[data-zpl-switch]'); }
  function setBusy(on, to) {
    Array.prototype.forEach.call(switches(), function (s) {
      s.classList.toggle('is-busy', !!on);
      if (on && to) s.setAttribute('data-zpl-to', to); else s.removeAttribute('data-zpl-to');
      if (on) s.setAttribute('aria-busy', 'true'); else s.removeAttribute('aria-busy');
    });
  }
  function updateSwitches() {
    Array.prototype.forEach.call(switches(), function (s) {
      s.classList.toggle('is-en', state.lang === 'en');
      s.classList.toggle('is-pl', state.lang !== 'en');
      s.setAttribute('aria-label', state.lang === 'en' ? 'Language' : 'Język');
      var cap = s.querySelector('.zpl-switch__caption');
      if (cap) cap.textContent = state.lang === 'en' ? 'Language' : 'Język';
      Array.prototype.forEach.call(s.querySelectorAll('[data-zpl-lang]'), function (a) {
        a.setAttribute('aria-current', a.getAttribute('data-zpl-lang') === state.lang ? 'true' : 'false');
      });
    });
  }
  /**
   * Switch theme follows the header's real text colour (the Suite decides per page/state whether the
   * header sits transparent over a dark hero). The mobile drawer is always light.
   */
  function luminance(c) {
    var m = String(c || '').match(/rgba?\(([^)]+)\)/);
    if (!m) return null;
    var p = m[1].split(/[\s,\/]+/).filter(Boolean).map(parseFloat);
    if (p.length > 3 && p[3] < 0.35) return null;
    return (0.2126 * p[0] + 0.7152 * p[1] + 0.0722 * p[2]) / 255;
  }
  function themeRef(s) {
    var nav = s.closest('#zpNewNav,.zpNewNav') || doc;
    if (s.classList.contains('zpl-switch--nav')) return nav.querySelector('.zpNewNav__link') || nav.querySelector('.zpNewNav__contactIcon');
    if (s.classList.contains('zpl-switch--chip')) return nav.querySelector('.zpNewNav__burger') || nav.querySelector('.zpNewNav__mobileCall');
    return null;
  }
  var themeRaf = 0;
  function syncTheme() {
    themeRaf = 0;
    Array.prototype.forEach.call(switches(), function (s) {
      /* v1.0.12 / Suite 2.2.809: header state already tells us whether controls sit on
         the dark hero. getComputedStyle(color) here forced a document-wide reflow (~247 ms). */
      var nav = s.closest('#zpNewNav,.zpNewNav');
      var dark = !!(nav && !s.classList.contains('zpl-switch--drawer') &&
        nav.classList.contains('zpNewNav--heroOverlay') &&
        !nav.classList.contains('is-scrolled') &&
        !nav.classList.contains('is-mega-open'));
      s.classList.toggle('zpl-switch--ondark', dark);
    });
    html.classList.add('zpl-theme');
  }
  function queueTheme() { if (!themeRaf) themeRaf = (window.requestAnimationFrame || setTimeout)(syncTheme); }
  function watchTheme() {
    syncTheme();
    var later = function () { queueTheme(); setTimeout(queueTheme, 220); setTimeout(queueTheme, 600); };
    window.addEventListener('scroll', queueTheme, { passive: true });
    window.addEventListener('resize', later, { passive: true });
    window.addEventListener('load', later);
    var nav = doc.getElementById('zpNewNav');
    if (nav && window.MutationObserver) {
      // Header state lives in classes on the nav and inline styles on its shell; link colours transition.
      new MutationObserver(later).observe(nav, { attributes: true, attributeFilter: ['class', 'style'], subtree: false });
      var shell = nav.querySelector('.zpNewNav__shell');
      if (shell) new MutationObserver(later).observe(shell, { attributes: true, attributeFilter: ['class', 'style'] });
    }
    later();
  }
  function bindSwitches() {
    doc.addEventListener('click', function (e) {
      var a = e.target && e.target.closest ? e.target.closest('[data-zpl-switch] [data-zpl-lang]') : null;
      if (!a) return;
      if (e.defaultPrevented || e.button > 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      e.preventDefault();
      var to = a.getAttribute('data-zpl-lang');
      if (to === state.lang) {
        // Tapping the active option toggles, which feels natural on the compact chip.
        var sw = a.closest('[data-zpl-switch]');
        if (sw && sw.classList.contains('zpl-switch--chip')) to = state.lang === 'en' ? 'pl' : 'en'; else return;
      }
      switchTo(to);
    }, true);
    var warm = function (e) {
      if (e.target && e.target.closest && e.target.closest('[data-zpl-switch]')) loadDict(state.lang === 'en' ? 'pl' : 'en');
    };
    doc.addEventListener('pointerover', warm, { passive: true });
    doc.addEventListener('focusin', warm);
    doc.addEventListener('touchstart', warm, { passive: true });
  }

  // ---------------------------------------------------------------- suite

  /** Header parent "Usługi" underline on English service pages (Suite matches Polish hrefs). */
  function suiteNav() {
    var svc = ['/strony-internetowe-katowice/', '/sklepy-internetowe-katowice/', '/logo-branding-katowice/', '/kampanie-reklamowe/'];
    if (svc.indexOf(cfg.src) < 0) return;
    Array.prototype.forEach.call(doc.querySelectorAll('#zpNewNav .zpNewNav__item[data-mega],#zpNewNav .zpNewNav__item[data-drop]'), function (item) {
      item.classList.add('is-active');
      var link = item.querySelector(':scope > .zpNewNav__link');
      if (link) link.classList.add('is-active');
    });
  }

  // ------------------------------------------------------------------- boot

  function sameSiteReferrer() {
    try { return !!doc.referrer && new URL(doc.referrer).host === location.host; } catch (e) { return false; }
  }
  function boot() {
    bindSwitches();
    updateSwitches();
    try { watchTheme(); } catch (e) {}
    if (state.lang === 'en') {
      startObserver();
      suiteNav();
      // Page scripts that ran before this one may have re-rendered parts of the page from Polish data.
      loadDict('en').then(function () {
        if (state.lang !== 'en') return;
        var col = collect(doc.body);
        withoutObserver(function () { applyCollected(col, 'en'); applyLinks(doc.body, 'en'); applyFrames(doc.body, 'en'); applyLangAttrs(doc.body, 'en'); applySvg(doc.body, 'en'); });
      });
    }
    window.ZPL = {
      lang: function () { return state.lang; },
      switchTo: switchTo,
      localize: localize,
      refresh: function () { if (state.lang === 'en') { queue.push(doc.body); flushQueue(); } },
      /** Current translation of a text (diagnostics, Skaner EN). */
      t: function (k, lang) { lang = lang || state.lang; return dicts[lang] ? translation(C.norm(k), lang) : undefined; },
      known: function (k, lang) { return known[lang || state.lang].has(C.norm(k)); },
      /** Resolves once queued text is translated and no server lookup is running (Skaner EN waits for it). */
      whenIdle: function (max) {
        var until = Date.now() + (max || 5000);
        return new Promise(function (resolve) {
          (function check() {
            var busy = state.busy || timer || queue.length || inflight > 0 || pending.en || pending.pl;
            if (!busy || Date.now() > until) resolve(!busy); else setTimeout(check, 40);
          }());
        });
      }
    };
    var pref = store.get('zpl_lang');
    // A scan frame (Skaner EN) always shows the language of its address.
    if (pref && pref !== state.lang && sameSiteReferrer() && !cfg.scan) switchTo(pref, { auto: true });
    if (cfg.debug && state.lang === 'en') loadDict('en').then(function () { applyCollected(collect(doc.body), 'en'); });
  }
  if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', boot); else boot();
}());
