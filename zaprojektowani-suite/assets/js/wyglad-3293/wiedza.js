/* ZP Suite 3.2.93 — Wiedza index: sort / topic chips / search / "show more".
   The server renders every card (and honours ?sort=&kat=&q=); this script only re-orders and hides cards. */
(function (w, d) {
  'use strict';
  var root = d.querySelector('[data-zp-kx]');
  if (!root) { return; }
  var grid = root.querySelector('[data-zp-kb-results]');
  if (!grid) { return; }
  var items = [].slice.call(grid.querySelectorAll('[data-zp-post]'));
  grid.classList.remove('is-paged');
  if (!items.length) { return; }

  var SORTS = ['newest', 'popular', 'oldest', 'longest', 'shortest', 'az'];
  var PAGE = parseInt(root.getAttribute('data-page'), 10) || 18;
  var q = function (sel) { return root.querySelector(sel); };
  var qa = function (sel) { return [].slice.call(root.querySelectorAll(sel)); };
  var input = q('[data-zp-kb-search]'), clear = q('[data-zp-kb-clear]'), reset = q('[data-zp-kb-reset]');
  var countEl = q('[data-zp-kb-count]'), wordEl = q('[data-zp-kb-word]'), emptyEl = q('[data-zp-kb-empty]');
  var pager = q('[data-zp-kb-pager]'), moreBtn = q('[data-zp-kb-more]'), shownEl = q('[data-zp-kb-shown]'), matchesEl = q('[data-zp-kb-matches]');
  var sortLinks = qa('[data-zp-sort]'), catLinks = qa('[data-zp-cat]'), viewBtns = qa('[data-zp-view]');

  function fold(s) {
    s = String(s || '').toLowerCase();
    try { s = s.normalize('NFD').replace(/[̀-ͯ]/g, ''); } catch (e) {}
    return s.replace(/ł/g, 'l').replace(/\s+/g, ' ').trim();
  }
  function text(el, sel) { var n = el.querySelector(sel); return n ? n.textContent : ''; }
  items.forEach(function (el, i) {
    var title = text(el, '.zpKX__title');
    el._zp = {
      reads: parseInt(el.getAttribute('data-reads'), 10) || 0,
      date: parseInt(el.getAttribute('data-date'), 10) || 0,
      min: parseInt(el.getAttribute('data-minutes'), 10) || 0,
      cat: ' ' + (el.getAttribute('data-cat') || '') + ' ',
      title: fold(title || el.getAttribute('data-title')),
      hay: fold(title + ' ' + text(el, '.zpKX__excerpt') + ' ' + (el.getAttribute('data-title') || '')),
      i: i,
      ok: true
    };
  });

  var cmp = {
    newest: function (a, b) { return (b.date - a.date) || (a.i - b.i); },
    oldest: function (a, b) { return (a.date - b.date) || (a.i - b.i); },
    popular: function (a, b) { return (b.reads - a.reads) || (b.date - a.date); },
    longest: function (a, b) { return (b.min - a.min) || (b.date - a.date); },
    shortest: function (a, b) { return (a.min - b.min) || (b.date - a.date); },
    az: function (a, b) { return a.title < b.title ? -1 : (a.title > b.title ? 1 : (b.date - a.date)); }
  };

  var state = { sort: 'newest', kat: 'all', q: '', limit: PAGE, view: 'grid' };
  function hasCat(k) { return catLinks.some(function (a) { return a.getAttribute('data-zp-cat') === k; }); }
  function readState() {
    var p = null;
    try { p = new URLSearchParams(w.location.search); } catch (e) {}
    var s = p ? (p.get('sort') || '') : '', k = p ? (p.get('kat') || '') : '';
    state.sort = SORTS.indexOf(s) > -1 ? s : 'newest';
    state.kat = (k && hasCat(k)) ? k : 'all';
    state.q = p ? (p.get('q') || '').slice(0, 80) : '';
    state.limit = PAGE;
    if (input && state.q && !input.value) { input.value = state.q; }
  }
  function writeState() {
    if (!w.history || !w.history.replaceState) { return; }
    var parts = [];
    if (state.sort !== 'newest') { parts.push('sort=' + encodeURIComponent(state.sort)); }
    if (state.kat !== 'all') { parts.push('kat=' + encodeURIComponent(state.kat)); }
    if (state.q) { parts.push('q=' + encodeURIComponent(state.q)); }
    try { w.history.replaceState(w.history.state, '', w.location.pathname + (parts.length ? '?' + parts.join('&') : '') + w.location.hash); } catch (e) {}
  }
  function plural(n) {
    if (!wordEl) { return; }
    var m10 = n % 10, m100 = n % 100;
    var form = n === 1 ? 'one' : ((m10 >= 2 && m10 <= 4 && !(m100 >= 12 && m100 <= 14)) ? 'few' : 'many');
    wordEl.textContent = wordEl.getAttribute('data-' + form) || wordEl.textContent;
  }
  function setActive(links, attr, value) {
    links.forEach(function (a) {
      var on = (a.getAttribute(attr) || '') === value;
      a.classList.toggle('is-active', on);
      a.setAttribute('aria-current', on ? 'true' : 'false');
    });
  }

  function apply(opts) {
    opts = opts || {};
    if (opts.resetLimit) { state.limit = PAGE; }
    var tokens = fold(state.q).split(' ').filter(Boolean);
    items.forEach(function (el) {
      var k = el._zp, ok = state.kat === 'all' || k.cat.indexOf(' ' + state.kat + ' ') > -1;
      for (var t = 0; ok && t < tokens.length; t++) { if (k.hay.indexOf(tokens[t]) === -1) { ok = false; } }
      k.ok = ok;
    });
    var sorted = items.slice().sort(function (a, b) { return (cmp[state.sort] || cmp.newest)(a._zp, b._zp); });
    var frag = d.createDocumentFragment(), matches = 0, shown = 0;
    sorted.forEach(function (el) {
      var vis = el._zp.ok && matches < state.limit;
      if (el._zp.ok) { matches++; }
      if (vis) { shown++; el.removeAttribute('hidden'); } else { el.setAttribute('hidden', ''); }
      frag.appendChild(el);
    });
    grid.appendChild(frag);
    if (countEl) { countEl.textContent = String(matches); }
    plural(matches);
    if (emptyEl) { emptyEl.hidden = matches > 0; }
    if (pager) { pager.hidden = !(matches > shown); }
    if (shownEl) { shownEl.textContent = String(shown); }
    if (matchesEl) { matchesEl.textContent = String(matches); }
    setActive(sortLinks, 'data-zp-sort', state.sort);
    setActive(catLinks, 'data-zp-cat', state.kat);
    if (clear) { clear.hidden = !state.q; }
    if (reset) { reset.hidden = (state.sort === 'newest' && state.kat === 'all' && !state.q); }
    root.setAttribute('data-sort', state.sort);
    root.setAttribute('data-kat', state.kat);
    if (!opts.silent) { writeState(); }
  }

  function keepInView() {
    var r = grid.getBoundingClientRect();
    if (r.top >= 0) { return; }
    var top = w.pageYOffset + root.getBoundingClientRect().top - 96;
    try { w.scrollTo({ top: top, behavior: 'smooth' }); } catch (e) { w.scrollTo(0, top); }
  }
  function focusShown(index) {
    var vis = items.filter(function (el) { return !el.hidden; });
    var el = vis[index] || vis[vis.length - 1];
    var a = el && el.querySelector('.zpKX__title a');
    if (a) { try { a.focus({ preventScroll: false }); } catch (e) { a.focus(); } }
  }

  sortLinks.forEach(function (a) {
    a.addEventListener('click', function (e) { e.preventDefault(); state.sort = a.getAttribute('data-zp-sort') || 'newest'; apply({ resetLimit: true }); keepInView(); });
  });
  catLinks.forEach(function (a) {
    a.addEventListener('click', function (e) { e.preventDefault(); state.kat = a.getAttribute('data-zp-cat') || 'all'; apply({ resetLimit: true }); keepInView(); });
  });
  [].slice.call(d.querySelectorAll('[data-zp-path-cat]')).forEach(function (a) {
    a.addEventListener('click', function (e) {
      var k = a.getAttribute('data-zp-path-cat') || 'all';
      if (!hasCat(k)) { return; }
      e.preventDefault();
      state.kat = k; apply({ resetLimit: true });
      try { root.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (err) { root.scrollIntoView(); }
    });
  });
  var timer = null;
  if (input) {
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(function () { state.q = input.value.trim().slice(0, 80); apply({ resetLimit: true }); }, 120);
    });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && input.value) { input.value = ''; state.q = ''; apply({ resetLimit: true }); }
    });
  }
  if (clear) { clear.addEventListener('click', function () { if (input) { input.value = ''; input.focus(); } state.q = ''; apply({ resetLimit: true }); }); }
  if (reset) { reset.addEventListener('click', function () { state.sort = 'newest'; state.kat = 'all'; state.q = ''; if (input) { input.value = ''; } apply({ resetLimit: true }); keepInView(); }); }
  if (moreBtn) { moreBtn.addEventListener('click', function () { var first = state.limit; state.limit += PAGE; apply(); focusShown(first); }); }

  function setView(v) {
    state.view = v;
    viewBtns.forEach(function (b) { var on = b.getAttribute('data-zp-view') === v; b.classList.toggle('is-active', on); b.setAttribute('aria-pressed', on ? 'true' : 'false'); });
    grid.classList.toggle('is-list', v === 'list');
    grid.classList.toggle('is-compact', v === 'compact');
    try { w.localStorage.setItem('zpKXView', v); } catch (e) {}
  }
  viewBtns.forEach(function (b) { b.addEventListener('click', function () { setView(b.getAttribute('data-zp-view') || 'grid'); }); });
  var saved = null;
  try { saved = w.localStorage.getItem('zpKXView'); } catch (e) {}
  if (saved && saved !== 'grid' && viewBtns.some(function (b) { return b.getAttribute('data-zp-view') === saved; })) { setView(saved); }

  var hint = q('[data-zp-kx-hint]'), catsRow = q('[data-zp-kx-cats]');
  if (hint && catsRow) { catsRow.addEventListener('scroll', function () { hint.hidden = true; }, { once: true, passive: true }); }

  readState();
  apply({ silent: true });
})(window, document);
