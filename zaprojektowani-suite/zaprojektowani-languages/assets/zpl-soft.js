/*! Zaprojektowani Languages — the language changes in place, without reloading the page (1.2.0).
 * Both versions of the page come from the server (the one on screen and the one in the new language) and are
 * read side by side: every text, attribute, link, head tag and data block of the page on screen that differs gets
 * the new language's version, under a cross-fade. Scripts keep running, the scroll position stays. When the two
 * versions do not line up (a different page, a redirect, scripts only the new page has), the caller opens the new
 * address the usual way. Parts that one version builds differently (English lists only the pages it has in the menus
 * and shows its own translations of the reviews) are taken whole from the new version. Hidden parts that scripts show
 * later (<template>, e.g. the consultation popup) change too.
 */
(function () {
  'use strict';
  var C = window.ZPLCore, doc = document, html = doc.documentElement;
  if (!C || window.ZPLSoft || !window.fetch || !window.DOMParser || !window.Map || !window.WeakMap || !window.history || !history.pushState) return;

  var LANGS = { pl: 1, en: 1, de: 1, es: 1 };
  var SAFE = /\/zaprojektowani-languages\/assets\/zpl(?:-core|-switch|-soft)?(?:\.min)?\.js$|\/(?:en|eur)-public-currency-\d+\.js$/;
  var HEAD = 'meta[name="description"],meta[name="robots"],meta[property^="og:"],meta[name^="twitter:"],link[rel="canonical"],link[rel="alternate"][hreflang],script[type="application/ld+json"]';
  // What a part taken whole from the new version may not hold: its own scripts, fields a visitor may be filling in, players.
  var UNSAFE = 'script,iframe,form,input,select,textarea,video,audio,canvas,object,embed,[data-zpl-switch]';
  var LINKS = 'a[href],area[href],form[action],iframe[src]';
  // Currency words: a price without other words ("3 000–6 000 zł", "PLN 3,000") is read as a number, since another
  // language may write it with no word at all ("700–1.400 €").
  var RX_MONEY = /zł|PLN|USD|EUR/g;
  var docs = {}, busy = false, applying = false, after = null, afterMap = null, afterQueue = [], afterTimer = 0, stat = null, taken = [];
  // The address whose version is on screen (after Back, location already shows the address being returned to).
  var shown = location.href;

  function each(list, fn) { Array.prototype.forEach.call(list || [], fn); }
  function abs(u) { try { return new URL(u, location.href); } catch (e) { return null; } }
  function pageKey(u) { var x = abs(u); return x ? x.origin + x.pathname + x.search : ''; }
  function cfgOf(d) { var el = d.getElementById('zpl-config'); if (!el) return null; try { return JSON.parse(el.textContent); } catch (e) { return null; } }
  function inSwitch(el) { return !!(el && el.closest && el.closest('[data-zpl-switch]')); }
  function langOf(path) { var m = /^\/(en|de|es)(?:\/|$)/.exec(path || '/'); return m ? m[1] : 'pl'; }
  function priceOnly(k) { return /\d/.test(k) && !C.human(C.norm(k.replace(RX_MONEY, ' '))); }
  /** Elements of a page that match `sel`, those inside <template> contents too, in their place. */
  function deep(root, sel) {
    var out = [];
    each(root.querySelectorAll(sel + ',template'), function (el) {
      if (C.tagName(el) !== 'template') out.push(el);
      else if (el.content) out.push.apply(out, deep(el.content, sel));
    });
    return out;
  }

  /** One page of the site as the server sends it (kept for the visit: the same page is never fetched twice). */
  function load(url) {
    var k = pageKey(url);
    if (!k) return Promise.resolve(null);
    if (docs[k]) return docs[k];
    var ctl = window.AbortController ? new AbortController() : null, timer = ctl ? setTimeout(function () { ctl.abort(); }, 12000) : 0;
    var p = fetch(k, { credentials: 'same-origin', headers: { Accept: 'text/html', 'X-ZPL-Soft': '1' }, signal: ctl ? ctl.signal : undefined })
      .then(function (r) {
        if (timer) clearTimeout(timer);
        if (!r.ok || !/text\/html/i.test(r.headers.get('content-type') || '')) return null;
        var final = r.url || k;
        return r.text().then(function (t) { return { url: final, doc: new DOMParser().parseFromString(t, 'text/html') }; });
      }).catch(function () { return null; });
    docs[k] = p;
    p.then(function (x) { if (!x) delete docs[k]; });
    return p;
  }

  // ---------------------------------------------------------------- reading both versions side by side

  /** Where a text sits: its kind and the tags of its element and three ancestors (classes may differ by language). */
  function sig(el) {
    var s = '';
    for (var p = el, n = 0; p && p.nodeType === 1 && n < 4; p = p.parentNode, n++) s += C.tagName(p) + '>';
    return s;
  }
  function collect(root) {
    var segs = [], attrs = [];
    if (!root) return { segs: segs, attrs: attrs };
    C.walk(root, function (s) {
      if (s.type === 'text' && priceOnly(s.k)) return;
      segs.push({ s: s, g: s.type + ':' + sig(s.type === 'multi' ? s.root : s.node.parentNode), i: segs.length, p: null });
    }, function (el) {
      if (!el.hasAttribute || inSwitch(el)) return;
      var items = C.attrItems(el);
      if (items.length) attrs.push({ el: el, items: items, g: C.tagName(el) + ':' + items.map(function (i) { return i[0]; }).join(',') });
    });
    return { segs: segs, attrs: attrs };
  }
  /** Pairs two very long lists in order; a few extra items on either side are skipped. */
  function greedy(a, b, fn) {
    var i = 0, j = 0, skipped = 0, LOOK = 40;
    while (i < a.length && j < b.length) {
      if (a[i].g === b[j].g) { fn(a[i], b[j]); i++; j++; continue; }
      var di = -1, dj = -1, x;
      for (x = 1; x <= LOOK && i + x < a.length; x++) { if (a[i + x].g === b[j].g) { di = x; break; } }
      for (x = 1; x <= LOOK && j + x < b.length; x++) { if (a[i].g === b[j + x].g) { dj = x; break; } }
      if (di < 0 && dj < 0) { i++; j++; skipped++; continue; }
      if (dj < 0 || (di >= 0 && di <= dj)) { i += di; skipped += di; } else { j += dj; skipped += dj; }
    }
    return skipped + (a.length - i) + (b.length - j);
  }
  /** The items of a list grouped by the elements with an id around them (outermost first). */
  function zones(list, elOf) {
    var root = { kids: [] }, nodes = new Map();
    list.forEach(function (x) {
      var chain = [], cur = root, i, n;
      for (var el = elOf(x); el && el.nodeType === 1; el = el.parentNode) { if (el.id && !/^(?:html|body)$/.test(C.tagName(el))) chain.push(el); }
      for (i = chain.length - 1; i >= 0; i--) {
        n = nodes.get(chain[i]);
        if (!n) { n = { g: '#' + chain[i].id, kids: [] }; nodes.set(chain[i], n); cur.kids.push(n); }
        cur = n;
      }
      cur.kids.push(x);
    });
    return root;
  }
  /** The longest run of equal keys of two lists (the same start and end skipped first), then inside each pair of elements. */
  function pairUp(ka, kb, fn) {
    var s = 0, ea = ka.length, eb = kb.length, pairs = [], i, j;
    while (s < ea && s < eb && ka[s].g === kb[s].g) s++;
    while (ea > s && eb > s && ka[ea - 1].g === kb[eb - 1].g) { ea--; eb--; }
    for (i = 0; i < s; i++) pairs.push(i, i);
    var n = ea - s, m = eb - s;
    if (n && m && n * m > 4000000) {
      var sa = [], sb = [];
      for (i = s; i < ea; i++) sa.push({ g: ka[i].g, k: i });
      for (j = s; j < eb; j++) sb.push({ g: kb[j].g, k: j });
      greedy(sa, sb, function (x, y) { pairs.push(x.k, y.k); });
    } else if (n && m) {
      var w = m + 1, L = new Uint16Array((n + 1) * w);
      for (i = n - 1; i >= 0; i--) {
        for (j = m - 1; j >= 0; j--) L[i * w + j] = ka[s + i].g === kb[s + j].g ? L[(i + 1) * w + j + 1] + 1 : Math.max(L[(i + 1) * w + j], L[i * w + j + 1]);
      }
      for (i = 0, j = 0; i < n && j < m;) {
        if (ka[s + i].g === kb[s + j].g) { pairs.push(s + i, s + j); i++; j++; }
        else if (L[(i + 1) * w + j] >= L[i * w + j + 1]) i++; else j++;
      }
    }
    for (i = 0; i < ka.length - ea; i++) pairs.push(ea + i, eb + i);
    for (i = 0; i < pairs.length; i += 2) {
      var x = ka[pairs[i]], y = kb[pairs[i + 1]];
      if (x.kids) { if (y.kids) pairUp(x.kids, y.kids, fn); } else if (!y.kids) fn(x, y);
    }
  }
  /**
   * Pairs the items of the two versions (texts, attributes, links) in order. Elements with an id are matched as one item
   * each and their own items are paired inside them, so a part that only one version has never shifts the rest.
   */
  function align(a, b, fn, elOf) {
    for (var i = 0; i < a.length && a.length === b.length; i++) { if (a[i].g !== b[i].g) break; }
    if (i === a.length && a.length === b.length) { a.forEach(function (x, k) { fn(x, b[k]); }); return; }
    pairUp(zones(a, elOf).kids, zones(b, elOf).kids, fn);
  }
  function hostOf(s) { return s.type === 'multi' ? s.root : s.node.parentNode; }
  function segEl(x) { return hostOf(x.s); }
  function itemEl(x) { return x.el; }

  /** Tag, id and class of an element and its place among the elements of its page that have the same ones. */
  function places(body) {
    var at = new Map(), by = new Map(), n = Object.create(null);
    each(body.getElementsByTagName('*'), function (el) {
      var k = C.tagName(el) + '#' + (el.id || '') + '.' + C.norm(el.getAttribute('class') || '');
      k += '\u0001' + (n[k] = (n[k] || 0) + 1);
      at.set(el, k); by.set(k, el);
    });
    return { at: at, by: by, n: n };
  }
  /** For every element, the first and last text of the list inside it; and the texts that found a partner. */
  function ranges(segs) {
    var m = new Map(), next = new Array(segs.length + 1), prev = new Array(segs.length), pre = [0], i, last = -1;
    segs.forEach(function (x) {
      for (var el = hostOf(x.s); el && el.nodeType === 1; el = el.parentNode) {
        var r = m.get(el);
        if (r) r[1] = x.i; else m.set(el, [x.i, x.i]);
      }
      pre.push(pre[x.i] + (x.p ? 1 : 0));
      if (x.p) last = x.i;
      prev[x.i] = last;
    });
    next[segs.length] = -1;
    for (i = segs.length - 1; i >= 0; i--) next[i] = segs[i].p ? i : next[i + 1];
    return { m: m, pre: pre, next: next, prev: prev };
  }
  /**
   * The parts to take whole from the new version: for every text without a partner, the smallest element around it
   * that both versions have (same tag, id and class at the same place) and whose texts that line up all line up inside
   * the same element of the other version. Parts inside other parts, or holding UNSAFE elements, are left out.
   */
  function blocks(A, B, a, b) {
    var pa = places(A.body), pb = places(B.body), ra = ranges(a.segs), rb = ranges(b.segs), found = new Map();
    function fits(ea, eb) {
      var x = ra.m.get(ea), y = rb.m.get(eb);
      var na = x ? ra.pre[x[1] + 1] - ra.pre[x[0]] : 0, nb = y ? rb.pre[y[1] + 1] - rb.pre[y[0]] : 0;
      // Nothing inside that lines up (and no id): an element like it, not necessarily the same one.
      if (na !== nb || (!na && !ea.id)) return false;
      if (!na) return true;
      var f = a.segs[ra.next[x[0]]], l = a.segs[ra.prev[x[1]]];
      return f.p.i >= y[0] && l.p.i <= y[1];
    }
    function around(x, own, other, isA) {
      var top = isA ? A.body : B.body;
      for (var el = hostOf(x.s); el && el.nodeType === 1 && el !== top; el = el.parentNode) {
        var twin = other.by.get(own.at.get(el));
        if (!twin) continue;
        var ea = isA ? el : twin, eb = isA ? twin : el;
        if (fits(ea, eb)) return ea;
      }
      return null;
    }
    var safe = new Map();
    function ok(ea) {
      if (!safe.has(ea)) {
        var eb = pb.by.get(pa.at.get(ea));
        safe.set(ea, !/^(?:html|head|body|main)$/.test(C.tagName(ea)) && !ea.matches(UNSAFE) && !eb.matches(UNSAFE) && !ea.querySelector(UNSAFE) && !eb.querySelector(UNSAFE));
      }
      return safe.get(ea);
    }
    // Parts only the old version has (e.g. the cities of Poland on Polish service pages): the outermost element around a
    // text with no partner that holds no text that lines up, of a kind the new version has fewer of, where the new
    // version has no text at all. The page on screen loses it.
    var gone = new Set();
    function alone(x) {
      var best = null, k, el;
      for (el = hostOf(x.s); el && el.nodeType === 1 && el !== A.body; el = el.parentNode) {
        k = ra.m.get(el);
        if (k && ra.pre[k[1] + 1] - ra.pre[k[0]]) break;
        best = el;
      }
      if (!best || /^(?:html|head|body|main)$/.test(C.tagName(best)) || best.querySelector('[data-zpl-switch]')) return null;
      var s = (pa.at.get(best) || '').split('\u0001')[0];
      if (!s || (pa.n[s] || 0) <= (pb.n[s] || 0)) return null;
      k = ra.m.get(best);
      var f = ra.prev[k[0]], l = ra.next[k[1] + 1];
      var bf = f >= 0 ? a.segs[f].p.i : -1, bl = l >= 0 ? a.segs[l].p.i : b.segs.length;
      return bl === bf + 1 ? best : null;
    }
    // Parts only the new version has (the same cities when coming back to Polish): the same rule the other way round,
    // for parts without UNSAFE elements. The page on screen gets them after the element before them.
    var added = new Set();
    function extra(y) {
      var best = null, k, el;
      for (el = hostOf(y.s); el && el.nodeType === 1 && el !== B.body; el = el.parentNode) {
        k = rb.m.get(el);
        if (k && rb.pre[k[1] + 1] - rb.pre[k[0]]) break;
        best = el;
      }
      if (!best || /^(?:html|head|body|main)$/.test(C.tagName(best)) || best.matches(UNSAFE) || best.querySelector(UNSAFE)) return null;
      var s = (pb.at.get(best) || '').split('\u0001')[0];
      if (!s || (pb.n[s] || 0) <= (pa.n[s] || 0)) return null;
      k = rb.m.get(best);
      var f = rb.prev[k[0]], l = rb.next[k[1] + 1];
      var af = f >= 0 ? b.segs[f].p.i : -1, al = l >= 0 ? b.segs[l].p.i : a.segs.length;
      return al === af + 1 ? best : null;
    }
    var lone = 0;
    a.segs.forEach(function (x) {
      if (x.p) return;
      var ea = around(x, pa, pb, true), d;
      if (ea && ok(ea)) found.set(ea, pb.by.get(pa.at.get(ea)));
      else if (within(hostOf(x.s), gone)) return;
      else if ((d = alone(x))) gone.add(d);
      else lone++;
    });
    b.segs.forEach(function (y) {
      if (y.p) return;
      var ea = around(y, pb, pa, false), d;
      if (ea && ok(ea)) found.set(ea, pb.by.get(pa.at.get(ea)));
      else if (within(hostOf(y.s), added)) return;
      else if ((d = extra(y))) added.add(d);
      else lone++;
    });
    var list = [], share = 0;
    found.forEach(function (eb, ea) {
      for (var up = ea.parentNode; up && up.nodeType === 1; up = up.parentNode) { if (found.has(up)) return; }
      var x = ra.m.get(ea);
      share += x ? x[1] - x[0] + 1 : 0;
      list.push({ a: ea, b: eb });
    });
    // Parts the server renders itself in each language (data-zpl-skip, e.g. the descriptions of the team photos):
    // the same elements with other texts or attributes are changed in place, a different build is taken whole.
    var fixes = [], seen = new Set(), inParts = new Set(list.map(function (k) { return k.a; }));
    function own(ea, eb) {
      if (!ea || !eb || seen.has(ea) || within(ea, inParts) || ea.innerHTML === eb.innerHTML) return;
      seen.add(ea);
      if (shape(ea) === shape(eb)) fixes.push({ a: ea, b: eb });
      else if (ok(ea)) { list.push({ a: ea, b: eb }); inParts.add(ea); }
    }
    each(A.body.querySelectorAll('[data-zpl-skip]'), function (ea) { if (!(ea.parentNode && ea.parentNode.closest && ea.parentNode.closest('[data-zpl-skip]'))) own(ea, pb.by.get(pa.at.get(ea))); });
    each(B.body.querySelectorAll('[data-zpl-skip]'), function (eb) { if (!(eb.parentNode && eb.parentNode.closest && eb.parentNode.closest('[data-zpl-skip]'))) own(pa.by.get(pb.at.get(eb)), eb); });
    var drop = [];
    gone.forEach(function (ea) {
      if (within(ea, inParts)) return;
      var x = ra.m.get(ea);
      share += x[1] - x[0] + 1;
      drop.push(ea);
    });
    // Each new part goes after the old version's twin of the element before it, or first into the twin of its parent.
    var add = [], inPartsB = new Set(list.map(function (k) { return k.b; }));
    added.forEach(function (eb) {
      if (within(eb, inPartsB)) return;
      var y = rb.m.get(eb), up = eb.parentNode, prev = eb.previousElementSibling;
      var upA = up === B.body ? A.body : pa.by.get(pb.at.get(up)), at = prev ? pa.by.get(pb.at.get(prev)) : upA;
      if (!upA || !at || (prev && at.parentNode !== upA)) { lone += y[1] - y[0] + 1; return; }
      share += y[1] - y[0] + 1;
      add.push({ b: eb, at: at, first: !prev });
    });
    return { list: list, lone: lone, share: share, fixes: fixes, drop: drop, add: add };
  }
  /** The elements and texts of a part, without their values: two parts of this shape can be changed in place. */
  function shape(el) {
    var s = '', w = el.ownerDocument.createTreeWalker(el, 5, null);
    for (var n = w.nextNode(); n; n = w.nextNode()) s += n.nodeType === 1 ? '<' + C.tagName(n) : '|';
    return s;
  }
  function nodesOf(el) {
    var out = [], w = el.ownerDocument.createTreeWalker(el, 5, null);
    for (var n = w.nextNode(); n; n = w.nextNode()) out.push(n);
    return out;
  }
  /** Part `ea` on screen (`el`) gets the texts and attributes of `eb`, where scripts have not changed them since. */
  function fix(el, ea, eb) {
    var la = nodesOf(ea), lb = nodesOf(eb), ll = nodesOf(el);
    if (la.length !== lb.length || la.length !== ll.length) return;
    for (var i = -1; i < la.length; i++) {
      var a = i < 0 ? ea : la[i], b = i < 0 ? eb : lb[i], l = i < 0 ? el : ll[i];
      if (a.nodeType !== l.nodeType || (a.nodeType === 1 && C.tagName(a) !== C.tagName(l))) return;
      if (a.nodeType === 3) { if (a.nodeValue !== b.nodeValue && l.nodeValue === a.nodeValue) l.nodeValue = b.nodeValue; continue; }
      each(b.attributes, function (at) { var v = a.getAttribute(at.name); if (v !== at.value && l.getAttribute(at.name) === v) l.setAttribute(at.name, at.value); });
      each(a.attributes, function (at) { if (!b.hasAttribute(at.name) && l.getAttribute(at.name) === at.value) l.removeAttribute(at.name); });
    }
  }
  /**
   * Numbers and prices written in each language's own way (4,9 and 4.9; 3 000 zł and 700 €) or counted for each
   * language (e.g. its own pages).
   */
  function numbers(root, skip) {
    var out = [];
    if (!root) return out;
    (function scan(r) {
      var w = (r.ownerDocument || r).createTreeWalker(r, 5, null);
      for (var n = w.nextNode(); n; n = w.nextNode()) {
        if (n.nodeType === 1) { if (C.tagName(n) === 'template' && n.content && !kept(n, skip)) scan(n.content); continue; }
        var v = C.norm(n.nodeValue || ''), el = n.parentNode;
        if (!v || !/\d/.test(v) || (C.human(v) && !priceOnly(v)) || !el || el.nodeType !== 1 || kept(el, skip)) continue;
        out.push({ el: el, node: n, v: v, g: 'num:' + sig(el) });
      }
    }(root));
    return out;
  }
  /** Inside a part no language changes, the language switch or (`skip`) a part taken whole. */
  function kept(el, skip) {
    for (var up = el; up && up.nodeType === 1; up = up.parentNode) { if (C.skipped(up) || inSwitch(up) || (skip && skip.has(up))) return true; }
    return false;
  }
  function linksOf(root, skip) {
    var out = [];
    deep(root, LINKS).forEach(function (el) {
      if (inSwitch(el) || (skip && within(el, skip))) return;
      var t = C.tagName(el), n = t === 'form' ? 'action' : (t === 'iframe' ? 'src' : 'href');
      out.push({ el: el, n: n, v: el.getAttribute(n), g: t + '.' + (el.getAttribute('class') || ''), p: null });
    });
    return out;
  }
  /** Hidden form fields that carry the page's language or address (thank-you page, source of the lead). */
  function fieldsOf(root) {
    return deep(root, 'input[type="hidden"][name]').map(function (el) { return { el: el, v: el.getAttribute('value') || '', g: el.getAttribute('name'), p: null }; });
  }
  function within(el, set) {
    for (var up = el; up && up.nodeType === 1; up = up.parentNode) { if (set.has(up)) return true; }
    return false;
  }

  function plan(A, B) {
    var a = collect(A.body), b = collect(B.body);
    var text = new Map(), ctx = new Map(), attr = new Map(), links = new Map(), hidden = new Map(), svg = new Map();
    align(a.segs, b.segs, function (x, y) { x.p = y; y.p = x; }, segEl);
    var parts = blocks(A, B, a, b), inA = new Set(), inB = new Set(), nums = new Map();
    parts.list.forEach(function (k) { inA.add(k.a); inB.add(k.b); });
    parts.add.forEach(function (k) { inB.add(k.b); });
    var na = numbers(A.body, inA);
    align(na, numbers(B.body, inB), function (x, y) {
      x.p = y;
      if (x.v !== y.v) nums.set(x.g + '\u0002' + x.v, y.v);
    }, itemEl);
    a.segs.forEach(function (x) {
      var y = x.p;
      if (y && within(hostOf(x.s), inA)) x.part = true;
      if (!y || x.part) return;
      var e = entryOf(x, y);
      if (!e) return;
      if (x.s.k !== y.s.k && !text.has(x.s.k)) text.set(x.s.k, e);
      ctx.set(x.g + '\u0002' + x.s.k, e);
    });
    a.attrs = a.attrs.filter(function (x) { return !within(x.el, inA); });
    b.attrs = b.attrs.filter(function (x) { return !within(x.el, inB); });
    align(a.attrs, b.attrs, function (x, y) {
      x.p = y;
      for (var i = 0; i < x.items.length && i < y.items.length; i++) {
        if (x.items[i][0] !== y.items[i][0] || x.items[i][1] === y.items[i][1]) continue;
        var k = x.items[i][0] + '\u0001' + x.items[i][1];
        if (!attr.has(k)) attr.set(k, y.items[i][1]);
      }
    }, itemEl);
    var la = linksOf(A, inA), ha = fieldsOf(A);
    align(la, linksOf(B, inB), function (x, y) {
      x.p = y;
      if (x.n === y.n && x.v !== y.v && !links.has(x.n + '\u0001' + x.v)) links.set(x.n + '\u0001' + x.v, y.v);
    }, itemEl);
    align(ha, fieldsOf(B), function (x, y) {
      x.p = y;
      if (x.v !== y.v) hidden.set(x.g + '\u0001' + x.v, y.v);
    }, itemEl);
    var sa = A.querySelectorAll('svg text, svg tspan'), sb = B.querySelectorAll('svg text, svg tspan');
    if (sa.length === sb.length) each(sa, function (el, i) {
      var u = C.norm(el.textContent || ''), v = C.norm(sb[i].textContent || '');
      if (!el.children.length && u && u !== v) svg.set(u, v);
    });
    return { segs: a.segs, attrs: a.attrs, la: la, ha: ha, na: na, text: text, ctx: ctx, attr: attr, links: links, hidden: hidden, svg: svg, nums: nums, parts: parts.list, fixes: parts.fixes, drop: parts.drop, add: parts.add, share: parts.share, off: parts.lone, total: Math.max(a.segs.length, b.segs.length) };
  }
  /** What text `x` of the old version becomes: text `y` of the new one (and a note on hover only one of them has). */
  function entryOf(x, y) {
    var e = null;
    if (x.s.k !== y.s.k) e = { k: y.s.k, t: y.s.type, aEls: x.s.els, bEls: y.s.els, bRoot: y.s.root };
    // A review translated from Polish says so on hover.
    var ha = hostOf(x.s).getAttribute('title'), hb = hostOf(y.s).getAttribute('title');
    if (ha === null && hb !== null) (e = e || { k: y.s.k, t: y.s.type }).title = hb;
    else if (ha !== null && hb === null) (e = e || { k: y.s.k, t: y.s.type }).title = '';
    return e;
  }
  /** Items on screen paired with the items of the version they come from (the same kind, place and value). */
  function sameAs(live, old, val, elOf) {
    var pos = new Map(), wrap = function (x) { return { x: x, el: elOf(x), g: x.g + '\u0002' + val(x) }; };
    align(live.map(wrap), old.map(wrap), function (l, a) { pos.set(l.x, a.x); }, itemEl);
    return pos;
  }
  function segKey(x) { return x.s.k; }
  function attrKey(x) { return x.items.map(function (i) { return i[1]; }).join('\u0003'); }
  function valKey(x) { return x.v; }

  // ---------------------------------------------------------------- writing the new language

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
  function rebuild(root, frag) {
    var f = root.firstChild, l = root.lastChild;
    var lead = f && f.nodeType === 3 ? (f.nodeValue.match(/^\s*/) || [''])[0] : '';
    var trail = l && l.nodeType === 3 ? (l.nodeValue.match(/\s*$/) || [''])[0] : '';
    while (root.firstChild) root.removeChild(root.firstChild);
    if (lead) root.appendChild(doc.createTextNode(lead));
    root.appendChild(frag);
    if (trail) root.appendChild(doc.createTextNode(trail));
  }
  /** Which element of the page on screen is element n of the new version (same tag and class, in order). */
  function remap(live, aEls, bEls) {
    var out = {}, used = {}, n, m;
    for (n in bEls) {
      var b = bEls[n], tag = C.tagName(b), cls = b.getAttribute('class') || '', hit = null;
      if (aEls[n] && !used[n] && C.tagName(aEls[n]) === tag && (aEls[n].getAttribute('class') || '') === cls) hit = n;
      for (m in aEls) { if (hit) break; if (!used[m] && C.tagName(aEls[m]) === tag && (aEls[m].getAttribute('class') || '') === cls) hit = m; }
      for (m in aEls) { if (hit) break; if (!used[m] && C.tagName(aEls[m]) === tag) hit = m; }
      if (!hit || !live[hit] || C.tagName(live[hit]) !== tag) return null;
      used[hit] = 1; out[n] = live[hit];
    }
    return out;
  }
  function put(s, e) {
    if (s.type === 'text') {
      if (e.t !== 'text') return false;
      var raw = s.node.nodeValue, lead = (raw.match(/^\s*/) || [''])[0], trail = (raw.match(/\s*$/) || [''])[0];
      s.node.nodeValue = lead + e.k + trail;
      return true;
    }
    if (e.t !== 'multi') return false;
    var toks = C.tokens(e.k), els = toks ? remap(s.els, e.aEls || {}, e.bEls || {}) : null;
    if (els) { rebuild(s.root, build(toks, { i: 0 }, 0, els)); return true; }
    // The new version arranges the inline elements differently: its own copy of them.
    if (!e.bRoot) return false;
    var frag = doc.createDocumentFragment();
    each(e.bRoot.childNodes, function (n) { frag.appendChild(doc.importNode(n, true)); });
    while (s.root.firstChild) s.root.removeChild(s.root.firstChild);
    s.root.appendChild(frag);
    return true;
  }
  /**
   * The texts on screen get the new language. With `exact` (the change itself) every text is first found at its place in
   * the old version, so a text that stands for different ones in different places gets the right one; texts scripts
   * added or moved are looked up by their words.
   */
  function writeTexts(p, root, exact) {
    var live = collect(root), n = 0, pos = exact ? sameAs(live.segs, p.segs, segKey, segEl) : null;
    live.segs.forEach(function (x) {
      var a = pos ? pos.get(x) : null, e, host = hostOf(x.s);
      if (a) { if (!a.p || a.part) return; e = entryOf(a, a.p); }
      else e = p.ctx.get(x.g + '\u0002' + x.s.k) || p.text.get(x.s.k);
      if (e && e.title !== undefined) { if (e.title === '') host.removeAttribute('title'); else if (!host.hasAttribute('title')) host.setAttribute('title', e.title); }
      if (e && e.k !== x.s.k && put(x.s, e)) n++;
    });
    var at = exact ? sameAs(live.attrs, p.attrs, attrKey, itemEl) : null;
    live.attrs.forEach(function (x) {
      var a = at ? at.get(x) : null;
      if (a) {
        if (a.p) x.items.forEach(function (it, i) { var bi = a.p.items[i]; if (bi && bi[0] === it[0] && bi[1] !== it[1]) x.el.setAttribute(it[0], bi[1]); });
        return;
      }
      x.items.forEach(function (it) { var v = p.attr.get(it[0] + '\u0001' + it[1]); if (v !== undefined) x.el.setAttribute(it[0], v); });
    });
    return n;
  }
  /**
   * What item `x` on screen becomes: with `pos` (the change itself) the new version of the item it is at its place in the
   * old version, else (items scripts added) the new version of the first old item with its value.
   */
  function newValue(x, pos, map, key) {
    var a = pos ? pos.get(x) : null;
    if (a) return a.p ? a.p.v : undefined;
    return map.get(key);
  }
  function writeNumbers(p, root, skip) {
    if (!p.nums.size) return;
    var live = numbers(root, skip), pos = sameAs(live, p.na, valKey, itemEl);
    live.forEach(function (x) {
      var v = newValue(x, pos, p.nums, x.g + '\u0002' + x.v);
      if (v === undefined || v === x.v) return;
      var raw = x.node.nodeValue;
      x.node.nodeValue = (raw.match(/^\s*/) || [''])[0] + v + (raw.match(/\s*$/) || [''])[0];
    });
  }
  function writeLinks(p, root, skip) {
    var live = linksOf(root, skip), pos = sameAs(live, p.la, valKey, itemEl);
    live.forEach(function (x) {
      var v = newValue(x, pos, p.links, x.n + '\u0001' + x.v);
      if (v !== undefined && v !== null && v !== x.v) x.el.setAttribute(x.n, v);
    });
    var fields = fieldsOf(root), at = sameAs(fields, p.ha, valKey, itemEl);
    fields.forEach(function (x) {
      var v = newValue(x, at, p.hidden, x.g + '\u0001' + x.v);
      if (v !== undefined && v !== x.v) { x.el.setAttribute('value', v); x.el.value = v; }
    });
    each(root.querySelectorAll('svg text, svg tspan'), function (el) {
      if (el.children.length) return;
      var v = p.svg.get(C.norm(el.textContent || ''));
      if (v !== undefined) el.textContent = v;
    });
  }
  function writeHead(B, from, to) {
    each(doc.head.querySelectorAll(HEAD), function (el) { el.parentNode.removeChild(el); });
    var frag = doc.createDocumentFragment();
    each(B.head.querySelectorAll(HEAD), function (el) { frag.appendChild(doc.importNode(el, true)); });
    doc.head.appendChild(frag);
    if (B.title) doc.title = B.title;
    // JSON-LD inside the page, in order.
    var la = doc.body.querySelectorAll('script[type="application/ld+json"]'), lb = B.body ? B.body.querySelectorAll('script[type="application/ld+json"]') : [];
    if (la.length === lb.length) each(la, function (el, i) { el.textContent = lb[i].textContent; });
    ['lang', 'data-zpl-page'].forEach(function (n) { var v = B.documentElement.getAttribute(n); if (v) html.setAttribute(n, v); });
    var rx = new RegExp('^' + from + '\\b', 'i'), v = B.documentElement.getAttribute('lang') || to;
    each(doc.body.querySelectorAll('[lang]'), function (el) { if (!inSwitch(el) && rx.test(el.getAttribute('lang') || '')) el.setAttribute('lang', v); });
  }
  function writeSwitches(B) {
    var news = B.querySelectorAll('[data-zpl-switch]');
    each(doc.querySelectorAll('[data-zpl-switch]'), function (cur) {
      var variant = /zpl-switch--(nav|chip|drawer)/.exec(cur.className || ''), hit = null;
      each(news, function (n) { if (!hit && variant && (n.className || '').indexOf('zpl-switch--' + variant[1]) >= 0) hit = n; });
      if (!hit) return;
      var fresh = doc.importNode(hit, true);
      if (cur.classList.contains('zpl-switch--ondark')) fresh.classList.add('zpl-switch--ondark');
      cur.parentNode.replaceChild(fresh, cur);
    });
  }
  /** Data blocks of page scripts (window.zp… = {…}): the same objects get the new language, so later renders use it. */
  function jsonAt(t, i) {
    var open = t.charAt(i);
    if (open !== '[' && open !== '{') return undefined;
    for (var j = i, depth = 0, str = false, esc = false; j < t.length; j++) {
      var c = t.charAt(j);
      if (str) { if (esc) esc = false; else if (c === '\\') esc = true; else if (c === '"') str = false; continue; }
      if (c === '"') str = true;
      else if (c === '[' || c === '{') depth++;
      else if (c === ']' || c === '}') { if (!--depth) { try { return JSON.parse(t.slice(i, j + 1)); } catch (e) { return undefined; } } }
    }
    return undefined;
  }
  function blobs(d) {
    var out = {};
    each(d.querySelectorAll('script:not([src])'), function (s) {
      var t = s.textContent || '', m = /^\s*window\.(zp\w+)\s*=\s*/.exec(t);
      if (!m) return;
      var v = jsonAt(t, m[0].length);
      if (v !== undefined) out[m[1]] = v;
    });
    return out;
  }
  function patch(live, a, b, map) {
    if (!live || typeof live !== 'object' || !a || !b || typeof a !== 'object' || typeof b !== 'object') return;
    Object.keys(live).forEach(function (k) {
      if (!Object.prototype.hasOwnProperty.call(a, k) || !Object.prototype.hasOwnProperty.call(b, k)) return;
      var lv = live[k], av = a[k], bv = b[k];
      if (typeof lv === 'string') {
        if (lv !== av || typeof bv !== 'string' || av === bv) return;
        try { live[k] = bv; } catch (e) { return; }
        var n = C.norm(av);
        if (n && C.human(n) && !map.has(n)) map.set(n, { k: C.norm(bv), t: 'text' });
      } else if (lv && typeof lv === 'object') patch(lv, av, bv, map);
    });
  }
  function writeData(A, B, p) {
    var a = blobs(A), b = blobs(B);
    Object.keys(a).forEach(function (name) { if (b[name] !== undefined) patch(window[name], a[name], b[name], p.text); });
  }
  /** The element on screen that is part `ea` of the version it shows: by id, else by tag and class and the same text. */
  function liveOf(ea, A) {
    var tag = C.tagName(ea);
    if (ea.id) { var el = doc.getElementById(ea.id); return el && C.tagName(el) === tag ? el : null; }
    var cls = (ea.getAttribute('class') || '').split(/\s+/).filter(Boolean), sel = tag, list, mine;
    cls.forEach(function (c) { sel += '.' + (window.CSS && CSS.escape ? CSS.escape(c) : c); });
    try { list = doc.body.querySelectorAll(sel); mine = A.body.querySelectorAll(sel); } catch (e) { return null; }
    var t = C.norm(ea.textContent || ''), hits = [], same = 0;
    each(list, function (el) { if (!inSwitch(el) && C.norm(el.textContent || '') === t) hits.push(el); });
    each(mine, function (el) { if (C.norm(el.textContent || '') === t) same++; });
    if (t && hits.length === 1 && same === 1) return hits[0];
    // Otherwise the same place among the elements like it (scripts may have reordered them: then only by text).
    if (list.length === mine.length) {
      var at = Array.prototype.indexOf.call(mine, ea), hit = at >= 0 ? list[at] : null;
      if (hit && (!t || hits.indexOf(hit) >= 0 || !hits.length)) return hit;
    }
    return null;
  }
  /**
   * Part `eb` of the new version in place of the content of `el` (part `ea` of the old one); attributes the server sets
   * differently in the two versions follow too (e.g. data-zpl-skip: English renders its reviews itself). Icons are
   * drawn like the ones already on the page.
   */
  function swap(el, ea, eb) {
    each(eb.attributes, function (at) { if (at.name !== 'class' && ea.getAttribute(at.name) !== at.value) el.setAttribute(at.name, at.value); });
    each(ea.attributes, function (at) { if (at.name !== 'class' && !eb.hasAttribute(at.name)) el.removeAttribute(at.name); });
    var frag = doc.createDocumentFragment(), drawn = {};
    each(el.querySelectorAll('svg[data-lucide]'), function (s) { drawn[s.getAttribute('data-lucide')] = s; });
    each(eb.childNodes, function (n) { frag.appendChild(doc.importNode(n, true)); });
    var left = 0;
    each(frag.querySelectorAll('i[data-lucide]'), function (i) {
      var name = i.getAttribute('data-lucide') || '', s = drawn[name];
      if (!s) { try { s = doc.querySelector('svg[data-lucide="' + (window.CSS && CSS.escape ? CSS.escape(name) : name) + '"]'); } catch (e) { s = null; } }
      if (!s) { left++; return; }
      var c = s.cloneNode(true);
      each(i.attributes, function (a) { if (a.name !== 'class' && a.name !== 'data-lucide') c.setAttribute(a.name, a.value); });
      c.setAttribute('class', ('lucide lucide-' + name + ' ' + (i.getAttribute('class') || '')).trim());
      i.parentNode.replaceChild(c, i);
    });
    while (el.firstChild) el.removeChild(el.firstChild);
    el.appendChild(frag);
    return left;
  }
  /** Icons no part on the page had yet: drawn by the page's own icon library, with the header's stroke. */
  function drawIcons() {
    var L = window.lucide;
    if (!L || typeof L.createIcons !== 'function') return;
    try { window.__zpAllowLucideScan = true; L.createIcons({ attrs: { 'stroke-width': 1.9, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' } }); } catch (e) {} finally { window.__zpAllowLucideScan = false; }
  }
  /** Prices shown by the currency scripts next to a calculator (USD, EUR) belong to the old language. */
  function dropCurrencyViews() {
    each(doc.querySelectorAll('[data-zp-usd-display],[data-zp-eur-display]'), function (v) {
      var o = v.previousElementSibling;
      if (o) { o.style.removeProperty('display'); if (o.getAttribute('aria-hidden') === 'true') o.removeAttribute('aria-hidden'); }
      if (v.parentNode) v.parentNode.removeChild(v);
    });
  }
  /** Classes the server gives <html> and <body> in one language only (scripts' own classes stay). */
  function writeClasses(A, B) {
    [['documentElement', html], ['body', doc.body]].forEach(function (x) {
      var a = A[x[0]], b = B[x[0]], live = x[1];
      if (!a || !b || !live) return;
      var ca = (a.getAttribute('class') || '').split(/\s+/).filter(Boolean), cb = (b.getAttribute('class') || '').split(/\s+/).filter(Boolean);
      ca.forEach(function (c) { if (cb.indexOf(c) < 0) live.classList.remove(c); });
      cb.forEach(function (c) { if (ca.indexOf(c) < 0) live.classList.add(c); });
    });
  }
  /** Stylesheets and style blocks of the new version (e.g. data/en.css with the English texts drawn by CSS). */
  function writeStyles(A, B) {
    var have = {};
    each(doc.querySelectorAll('link[rel="stylesheet"][href]'), function (l) { var u = abs(l.getAttribute('href')); if (u) have[u.pathname] = 1; });
    each(B.querySelectorAll('link[rel="stylesheet"][href]'), function (l) {
      var u = abs(l.getAttribute('href'));
      if (!u || have[u.pathname] || u.origin !== location.origin) return;
      have[u.pathname] = 1;
      doc.head.appendChild(doc.importNode(l, true));
    });
    each(B.querySelectorAll('style[id]'), function (s) {
      var cur = doc.getElementById(s.id), old = A.getElementById(s.id);
      if (!cur) { (s.closest('head') ? doc.head : doc.body).appendChild(doc.importNode(s, true)); return; }
      if (C.tagName(cur) === 'style' && old && old.textContent !== s.textContent && cur.textContent === old.textContent) cur.textContent = s.textContent;
    });
  }
  /** Scripts and their data that only the new version has (zpl.js, the currency script and its settings). */
  function missingScripts(B) {
    var have = {}, list = [], bad = false;
    each(doc.querySelectorAll('script[src]'), function (s) { var u = abs(s.getAttribute('src')); if (u) have[u.pathname] = 1; });
    each(B.querySelectorAll('script[src]'), function (s) {
      var src = s.getAttribute('src'), u = abs(src);
      if (!u || have[u.pathname]) return;
      have[u.pathname] = 1;
      if (u.origin !== location.origin || !SAFE.test(u.pathname)) { bad = true; return; }
      list.push(u.href);
    });
    return bad ? null : list;
  }
  function addScripts(B, list) {
    each(B.querySelectorAll('script[type="application/json"][id]'), function (s) {
      if (s.id === 'zpl-config') return;
      var cur = doc.getElementById(s.id);
      if (cur) cur.textContent = s.textContent; else doc.body.appendChild(doc.importNode(s, true));
    });
    list.forEach(function (src) {
      var el = doc.createElement('script');
      el.src = src; el.async = false;
      doc.body.appendChild(el);
    });
  }

  // ---------------------------------------------------------------- texts added later by page scripts

  function watch(map) {
    afterMap = map;
    if (after || !window.MutationObserver) return;
    after = new MutationObserver(function (records) {
      if (applying || !afterMap) return;
      records.forEach(function (r) { if (r.type === 'childList') each(r.addedNodes, function (n) { afterQueue.push(n); }); else afterQueue.push(r.target); });
      if (!afterTimer) afterTimer = setTimeout(flush, 60);
    });
    after.observe(doc.body, { childList: true, subtree: true, characterData: true });
  }
  function flush() {
    afterTimer = 0;
    var list = afterQueue.splice(0), seen = new Set();
    if (!afterMap) return;
    applying = true;
    try {
      list.forEach(function (n) {
        var el = n.nodeType === 1 ? n : n.parentNode;
        if (!el || el.nodeType !== 1 || !el.isConnected || seen.has(el) || inSwitch(el)) return;
        // Parts taken whole from the new version are already in it (page scripts may move them around).
        if (taken.some(function (t) { return t.contains(el); })) return;
        seen.add(el);
        writeTexts(afterMap, el);
      });
    } finally { if (after) after.takeRecords(); applying = false; }
  }

  // ---------------------------------------------------------------- the change itself

  function animate(fn) {
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce) { fn(); return Promise.resolve(); }
    if (doc.startViewTransition) {
      try {
        html.classList.add('zpl-vt-soft');
        var t = doc.startViewTransition(fn);
        var done = function () { html.classList.remove('zpl-vt-soft'); };
        t.finished.then(done, done);
        if (t.ready && t.ready.catch) t.ready.catch(function () {});
        return t.updateCallbackDone;
      } catch (e) { html.classList.remove('zpl-vt-soft'); }
    }
    return new Promise(function (resolve) {
      html.classList.add('zpl-soft-out');
      setTimeout(function () {
        try { fn(); } finally {
          html.classList.remove('zpl-soft-out'); html.classList.add('zpl-soft-in');
          setTimeout(function () { html.classList.remove('zpl-soft-in'); }, 320);
          resolve();
        }
      }, 150);
    });
  }

  /** Prepare a change: the version on screen (and the page in the new language, when known) are fetched ahead. */
  function prepare(href) { load(shown); if (href) load(href); }

  /** Pages a soft change can no longer use (e.g. the visitor's language choice changed what the server sends). */
  function forget() { docs = {}; }

  /** Change to language `to` whose address is `href`. Resolves true when done in place, false when the caller should navigate. */
  function run(to, href, opts) {
    opts = opts || {};
    if (busy || !LANGS[to]) return Promise.resolve(false);
    var target = abs(href);
    if (!target || target.origin !== location.origin) return Promise.resolve(false);
    busy = true;
    var here = opts.push === false ? shown : location.href;
    return Promise.all([load(here), load(target.href)]).then(function (r) {
      var A = r[0], B = r[1];
      if (!A || !B) return false;
      var ca = cfgOf(A.doc), cb = cfgOf(B.doc), cur = cfgOf(doc);
      var finalUrl = abs(B.url);
      // The same page in another language, at the address asked for, rendered by this version of the plugin.
      if (!ca || !cb || !cur || cb.lang !== to || ca.src !== cb.src || ca.src !== cur.src || ca.lang !== cur.lang) return false;
      if (!finalUrl || finalUrl.pathname !== target.pathname) return false;
      if (ca.v !== cur.v) return false;
      [ca.attrs, cb.attrs].forEach(function (x) { if (x && C.addAttrs) C.addAttrs(x); });
      var scripts = missingScripts(B.doc);
      if (scripts === null) return false;
      var p = plan(A.doc, B.doc);
      // What the last change found (ZPLSoft.stat() in the console, when a change reloads the page).
      stat = { texts: p.total, unmatched: p.off, parts: p.parts.length, dropped: p.drop.length, added: p.add.length, inParts: p.share };
      // More than a few texts that do not line up, or most of the page built differently: the page is another one.
      if (p.total > 0 && (p.off / p.total > 0.04 || p.share / p.total > 0.5)) return false;
      var parts = [], fixes = [];
      for (var i = 0; i < p.parts.length; i++) {
        var el = liveOf(p.parts[i].a, A.doc);
        if (!el || parts.some(function (x) { return x.el === el || x.el.contains(el) || el.contains(x.el); })) return false;
        parts.push({ el: el, a: p.parts[i].a, b: p.parts[i].b });
      }
      stat.where = parts.map(function (x) { return C.tagName(x.el) + (x.el.id ? '#' + x.el.id : '') + (x.el.getAttribute('class') ? '.' + x.el.getAttribute('class').split(/\s+/)[0] : ''); });
      p.fixes.forEach(function (f) { var el = liveOf(f.a, A.doc); if (el) fixes.push({ el: el, a: f.a, b: f.b }); });
      var drops = [];
      for (i = 0; i < p.drop.length; i++) {
        var g = liveOf(p.drop[i], A.doc);
        if (!g || inSwitch(g) || g.querySelector('[data-zpl-switch]') || parts.some(function (x) { return x.el.contains(g) || g.contains(x.el); })) return false;
        drops.push(g);
      }
      var adds = [];
      for (i = 0; i < p.add.length; i++) {
        var at = p.add[i].at === A.doc.body ? doc.body : liveOf(p.add[i].at, A.doc);
        if (!at || inSwitch(at) || parts.some(function (x) { return x.el.contains(at); }) || drops.some(function (g) { return g.contains(at); })) return false;
        adds.push({ at: at, b: p.add[i].b, first: p.add[i].first });
      }
      var from = cur.lang || 'pl';
      try { doc.dispatchEvent(new CustomEvent('zpl:before-change', { detail: { lang: to, from: from, soft: true } })); } catch (e) {}
      return animate(function () {
        applying = true;
        try {
          taken = parts.map(function (x) { return x.el; });
          var skip = new Set(taken), left = 0;
          dropCurrencyViews();
          writeData(A.doc, B.doc, p);
          writeTexts(p, doc.body, true);
          writeNumbers(p, doc.body, skip);
          writeLinks(p, doc, skip);
          fixes.forEach(function (x) { fix(x.el, x.a, x.b); });
          parts.forEach(function (x) { left += swap(x.el, x.a, x.b); });
          drops.forEach(function (g) { if (g.parentNode) g.parentNode.removeChild(g); });
          adds.forEach(function (x) {
            var el = doc.importNode(x.b, false);
            if (x.first) x.at.insertBefore(el, x.at.firstChild); else x.at.parentNode.insertBefore(el, x.at.nextSibling);
            left += swap(el, x.b, x.b);
            taken.push(el);
          });
          if (left) drawIcons();
          writeHead(B.doc, from, to);
          writeClasses(A.doc, B.doc);
          writeStyles(A.doc, B.doc);
          writeSwitches(B.doc);
          var ce = doc.getElementById('zpl-config'), cn = B.doc.getElementById('zpl-config');
          if (ce && cn) ce.textContent = cn.textContent;
          var url = target.pathname + location.search + location.hash;
          if (opts.push !== false) history.pushState({ zplSoft: to }, '', url);
          shown = location.href;
        } finally { if (after) after.takeRecords(); applying = false; }
      }).then(function () {
        // zpl.js reads the whole page again when it starts after a change like this one.
        api.changed = true;
        addScripts(B.doc, scripts);
        watch(p);
        try { doc.dispatchEvent(new CustomEvent('zpl:change', { detail: { lang: to, from: from, soft: true } })); } catch (e) {}
        return true;
      });
    }).catch(function (e) { if (window.console) console.warn('[zpl]', e); return false; }).then(function (ok) { busy = false; return ok; });
  }

  // Back and forward between the language versions: the same change, without a new history entry.
  window.addEventListener('popstate', function () {
    var cur = cfgOf(doc);
    if (!cur) return;
    var path = location.pathname, base = cur.base || '';
    if (base && path.indexOf(base) === 0) path = path.slice(base.length) || '/';
    var to = langOf(path);
    if ((cur.lang || 'pl') === to) return;
    run(to, location.href, { push: false }).then(function (ok) { if (!ok) location.reload(); });
  });

  var api = window.ZPLSoft = { run: run, prepare: prepare, load: load, forget: forget, stat: function () { return stat; }, changed: false };
}());
