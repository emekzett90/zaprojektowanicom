/*! German and Spanish price presentation in euro ("ab 940 €", "desde 1.550 €"). Original form and calculator values stay PLN. */
(function () {
  'use strict';
  var cfg = read();
  if (!cfg) return;
  function read() {
    var el = document.getElementById('zp-eur-currency-config'), c;
    if (!el) return null;
    try { c = JSON.parse(el.textContent); } catch (e) { return null; }
    if (!c || c.policy !== 'eur_nbp_human_v1' || !Number.isInteger(c.mid_scaled) || c.mid_scaled < 10000 || c.mid_scaled > 1000000000 || !/^\d{4}-\d{2}-\d{2}$/.test(c.effective_date)) return null;
    return c;
  }
  var mid = cfg.mid_scaled, deferZl = false;
  var sp = '[\\s\\u00a0\\u202f]';
  var numberPattern = '(?:[0-9]{1,3}(?:[ .\\u00a0\\u202f][0-9]{3})+|[0-9]+)(?:,[0-9]{1,2})?';
  var chain = numberPattern + '(?:' + sp + '*(?:–|—|-|bis|und|a|y|hasta)' + sp + '*' + numberPattern + '|,' + sp + '+' + numberPattern + '){0,4}';
  var moneyPattern = new RegExp('(?<![\\p{L}\\p{N}.,-])(?:(PLN)' + sp + '+(' + chain + ')|(' + chain + ')' + sp + '*(PLN|zł))(?![\\p{L}\\p{N}]|[.,][0-9])', 'giu');
  function amount(s) {
    s = s.replace(/[  ]/g, ' ').trim();
    if (!/^(?:[0-9]{1,3}(?:[ .][0-9]{3})+|[0-9]+)(?:,[0-9]{1,2})?$/.test(s)) return null;
    var p = s.replace(/[ .]/g, '').split(','); if (p[0].length > 8 || Number(p[0]) > 10000000) return null;
    var cents = Number(p[0]) * 100 + Number(((p[1] || '') + '00').slice(0, 2)); return cents <= 1000000000 ? cents : null;
  }
  function schemaAmount(s) {
    if (/^[0-9]{1,8}(?:\.[0-9]{1,2})?$/.test(s)) { var p = s.split('.'); return Number(p[0]) * 100 + Number(((p[1] || '') + '00').slice(0, 2)); }
    return amount(s);
  }
  function eur(cents) {
    if (!Number.isInteger(cents) || cents < 0 || cents > 1000000000) return null;
    if (!cents) return 0;
    var n = cents * 10000, step = n < 10 * mid ? 1 : n < 100 * mid ? 5 : n < 1000 * mid ? 10 : 50;
    return Math.max(1, Math.floor((n * 2 + step * mid) / (2 * step * mid)) * step);
  }
  function format(n) { return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
  function edits(text) {
    var out = [], m; moneyPattern.lastIndex = 0;
    while ((m = moneyPattern.exec(text))) {
      // A Polish amount ("15 000 zł") that a page script has just written waits for the translation first:
      // its patterns ("Budżet: {1} zł" => "Budget: {1} zł") need the Polish form. late() converts what stays.
      if (deferZl && m[4] && m[4].toLowerCase() === 'zł') continue;
      var raw = m[2] || m[3], groupOffset = m.index + (m[1] ? m[0].indexOf(raw, m[1].length) : 0);
      var nums = new RegExp(numberPattern, 'gu'), n, part = [], valid = true;
      while ((n = nums.exec(raw))) { var cents = amount(n[0]); if (cents === null) { valid = false; break; } part.push([groupOffset + n.index, n[0].length, format(eur(cents))]); }
      if (!valid || !part.length) continue;
      var end = groupOffset + raw.length;
      // "940 €" with a no-break space; the sign once after the last amount ("470–1.200 €").
      if (m[1]) { out.push([m.index, groupOffset - m.index, '']); part[part.length - 1][2] += ' €'; }
      else out.push([end, m.index + m[0].length - end, ' €']);
      out = out.concat(part);
    }
    return out.sort(function (a, b) { return b[0] - a[0]; });
  }
  function money(text) { edits(text).forEach(function (e) { text = text.slice(0, e[0]) + e[2] + text.slice(e[0] + e[1]); }); return text; }
  function schema(value, inherited) {
    if (!value || typeof value !== 'object') return typeof value === 'string' ? money(value) : value;
    if (Array.isArray(value)) return value.map(function (v) { return schema(v, inherited); });
    var types = value['@type']; types = Array.isArray(types) ? types : [types];
    var typed = types.some(function (t) { return ['Offer', 'AggregateOffer', 'PriceSpecification', 'UnitPriceSpecification'].indexOf(t) >= 0; });
    var currency = typeof value.priceCurrency === 'string' ? value.priceCurrency : inherited;
    var numeric = {}, valid = true, original = value.description, result = {};
    if (typed && currency === 'PLN') ['price', 'minPrice', 'maxPrice', 'lowPrice', 'highPrice'].forEach(function (k) {
      if (!Object.prototype.hasOwnProperty.call(value, k)) return;
      var raw = value[k], cents = (typeof raw === 'string' || typeof raw === 'number') ? schemaAmount(String(raw)) : null;
      if (cents === null) valid = false; else numeric[k] = eur(cents);
    });
    if (!valid) return value;
    Object.keys(value).forEach(function (k) {
      result[k] = Object.prototype.hasOwnProperty.call(numeric, k) ? (typeof value[k] === 'string' ? String(numeric[k]) : numeric[k]) : k === 'priceCurrency' ? value[k] : schema(value[k], typed ? currency : '');
    });
    if (typed && currency === 'PLN' && (Object.keys(numeric).length || JSON.stringify(result).indexOf('"priceCurrency":"EUR"') >= 0 || result.description !== original)) result.priceCurrency = 'EUR';
    return result;
  }
  var skip = /^(SCRIPT|STYLE|TEXTAREA|CODE|PRE|SVG|MATH|IFRAME|OBJECT|CANVAS|NOSCRIPT|TEMPLATE)$/;
  var inline = /^(A|ABBR|B|BDI|BDO|CITE|DEL|EM|I|INS|MARK|Q|S|SMALL|SPAN|STRONG|SUB|SUP|U|VAR)$/;
  var calculationViews = new WeakMap();
  function calculationSource(node) { return /(?:^|\s)(?:zpbsMiniCard__price|zpbsPkgPrice)(?:\s|$)/.test(node.getAttribute('class') || ''); }
  function collect(node, nodes, state) {
    if (node.nodeType === 3) { if (state.text && node.nodeValue && !/\s$/.test(state.text) && !/^\s/.test(node.nodeValue)) state.text += ' '; nodes.push({ node: node, offset: state.text.length, text: node.nodeValue }); state.text += node.nodeValue; return true; }
    if (node.nodeType !== 1 || !inline.test(node.tagName) || node.hasAttribute('data-zp-currency-skip') || node.hasAttribute('data-zp-usd-display') || node.id === 'wpadminbar' || calculationSource(node)) return false;
    for (var i = 0; i < node.childNodes.length; i++) if (!collect(node.childNodes[i], nodes, state)) return false;
    return true;
  }
  function freezeOptions(root) {
    var options = root.querySelectorAll ? root.querySelectorAll('option:not([value])') : [];
    Array.prototype.forEach.call(options, function (option) { if (/\bPLN\b|zł/.test(option.textContent)) option.setAttribute('value', option.value); });
  }
  function attributes(node) {
    ['title', 'aria-label', 'aria-valuetext', 'label'].forEach(function (name) { if (node.hasAttribute(name)) { var old = node.getAttribute(name), v = money(old); if (v !== old) node.setAttribute(name, v); } });
    if (node.tagName === 'META' && /^(description|og:title|og:description|twitter:title|twitter:description)$/.test((node.getAttribute('name') || node.getAttribute('property') || '').toLowerCase())) { var old = node.getAttribute('content') || '', v = money(old); if (v !== old) node.setAttribute('content', v); }
  }
  function inlineAttributes(node) {
    if (node.nodeType !== 1 || node.hasAttribute('data-zp-currency-skip') || node.id === 'wpadminbar' || calculationSource(node)) return;
    attributes(node); Array.prototype.forEach.call(node.childNodes, inlineAttributes);
  }
  // The Quote Studio reads its package prices back from these elements: they keep złoty, a euro copy is shown instead.
  function calculationDisplay(node) {
    var converted = money(node.textContent), saved = calculationViews.get(node);
    if (converted === node.textContent) {
      if (saved) { saved.view.remove(); node.style.cssText = saved.style; if (saved.aria === null) node.removeAttribute('aria-hidden'); else node.setAttribute('aria-hidden', saved.aria); calculationViews.delete(node); }
      return;
    }
    if (saved && saved.text === node.textContent && saved.view.isConnected) return;
    var view = node.cloneNode(true); view.removeAttribute('id'); Array.prototype.forEach.call(view.querySelectorAll('[id]'), function (el) { el.removeAttribute('id'); });
    walk(view, true); view.setAttribute('data-zp-currency-skip', ''); view.setAttribute('data-zpl-skip', ''); view.setAttribute('data-zp-eur-display', ''); view.removeAttribute('aria-hidden');
    var originalStyle = saved ? saved.style : node.style.cssText, originalAria = saved ? saved.aria : node.getAttribute('aria-hidden'); view.style.cssText = originalStyle;
    if (saved) saved.view.remove(); node.insertAdjacentElement('afterend', view); node.style.setProperty('display', 'none', 'important'); node.setAttribute('aria-hidden', 'true');
    calculationViews.set(node, { view: view, text: node.textContent, style: originalStyle, aria: originalAria });
  }
  function walk(node, displayOnly) {
    if (node.nodeType === 3) { var newText = money(node.nodeValue); if (newText !== node.nodeValue) node.nodeValue = newText; return; }
    if (node.nodeType !== 1) return;
    if (node.id === 'wpadminbar' || node.hasAttribute('data-zp-currency-skip') || node.hasAttribute('data-zp-usd-display')) return;
    if (!displayOnly && calculationSource(node)) { var deferred = deferZl; deferZl = false; calculationDisplay(node); deferZl = deferred; return; }
    if (node.tagName === 'SCRIPT') {
      if ((node.getAttribute('type') || '').toLowerCase() === 'application/ld+json') try { var raw = node.textContent, parsed = JSON.parse(raw), converted = schema(parsed, ''); if (JSON.stringify(parsed) !== JSON.stringify(converted)) node.textContent = JSON.stringify(converted); } catch (e) {}
      return;
    }
    if (skip.test(node.tagName)) return;
    attributes(node);
    var nodes = [], state = { text: '' }, single = !/^(HTML|HEAD|BODY)$/.test(node.tagName);
    if (single) for (var i = 0; i < node.childNodes.length; i++) if (!collect(node.childNodes[i], nodes, state)) { single = false; break; }
    if (single && nodes.length) {
      var changes = edits(state.text), perNode = new Map(), valid = true;
      changes.forEach(function (change) { var matched = false; nodes.some(function (n) { if (change[0] >= n.offset && change[0] + change[1] <= n.offset + n.text.length) { var list = perNode.get(n) || []; list.push([change[0] - n.offset, change[1], change[2]]); perNode.set(n, list); matched = true; return true; } return false; }); if (!matched) valid = false; });
      if (valid) perNode.forEach(function (list, n) { var text = n.text; list.forEach(function (e) { text = text.slice(0, e[0]) + e[2] + text.slice(e[0] + e[1]); }); if (text !== n.node.nodeValue) n.node.nodeValue = text; });
      Array.prototype.forEach.call(node.childNodes, inlineAttributes);
    } else Array.prototype.slice.call(node.childNodes).forEach(function (child) { walk(child, displayOnly); });
  }
  var observer = null, queued = [], frameDocs = new WeakSet(), raf = 0, later = new Map(), lateTimer = 0;
  // German or Spanish on screen: after a change to Polish or English (without a reload) this script does nothing.
  function active() { return /^(?:de|es)(?:-|$)/i.test(document.documentElement.lang || ''); }
  function priceRoot(node) {
    var el = node.nodeType === 1 ? node : node.parentElement; if (!el) return null;
    for (var p = el; p; p = p.parentElement) if (p.hasAttribute('data-zp-currency-skip') || p.hasAttribute('data-zp-usd-display') || p.id === 'wpadminbar' || (skip.test(p.tagName) && !(p.tagName === 'SCRIPT' && (p.getAttribute('type') || '').toLowerCase() === 'application/ld+json'))) return null;
    while (el.parentElement && inline.test(el.tagName)) el = el.parentElement; return el;
  }
  function queue(records) {
    if (!active()) return;
    records.forEach(function (r) { var root = priceRoot(r.target); if (root && queued.indexOf(root) < 0 && queued.length < 128) queued.push(root); });
    if (!raf && queued.length) raf = requestAnimationFrame(flush);
  }
  function flush() {
    raf = 0; var work = queued; queued = []; if (!active()) return; if (observer) observer.disconnect();
    deferZl = true;
    try { work.forEach(function (root) { if (root.isConnected) { freezeOptions(root); walk(root); } }); } finally { deferZl = false; }
    observe();
    var now = Date.now();
    work.forEach(function (root) { if (later.has(root) || later.size < 512) later.set(root, now); });
    if (later.size && !lateTimer) lateTimer = setTimeout(late, 400);
  }
  // A root is converted in full 400 ms after its last change, when the translation's own pass is done.
  function late() {
    lateTimer = 0; if (!active()) { later.clear(); return; }
    var now = Date.now(), due = [], next = 400;
    later.forEach(function (at, root) { if (now - at >= 400) { due.push(root); later.delete(root); } else next = Math.min(next, 410 - (now - at)); });
    if (due.length) { if (observer) observer.disconnect(); due.forEach(function (root) { if (root.isConnected) walk(root); }); observe(); }
    if (later.size) lateTimer = setTimeout(late, next);
  }
  function observe() { if (observer) observer.observe(document.documentElement, { subtree: true, childList: true, characterData: true, attributes: true, attributeFilter: ['content', 'title', 'aria-label', 'aria-valuetext', 'label'] }); }
  function ownedFrame(frame) {
    if (cfg.source !== '/studio-wyceny/') return false;
    try {
      var u = new URL(frame.src, location.href); if (u.origin !== location.origin || u.pathname !== '/' || u.searchParams.get('zpl_frame') !== 'studio-wyceny-v19/chooser-v19.html') return false;
      var valid = true;
      u.searchParams.forEach(function (v, k) { if (k === 'zpl_frame') return; if (k === 'zpl_lang' && /^(?:de|es)$/.test(v)) return; if (k === 'v' && /^[A-Za-z0-9._-]{1,32}$/.test(v)) return; if (k === 'n' && /^\d{1,4}$/.test(v)) return; valid = false; });
      return valid;
    } catch (e) { return false; }
  }
  function convertFrame(frame) {
    if (!active() || !ownedFrame(frame)) return;
    try {
      var doc = frame.contentDocument; if (!doc || !doc.body || !/^(?:de|es)(?:-|$)/i.test(doc.documentElement.lang || '')) return;
      freezeOptions(doc); walk(doc.documentElement);
      if (!frameDocs.has(doc) && window.MutationObserver) {
        frameDocs.add(doc);
        var ob = new MutationObserver(function (records) { if (!active()) return; ob.disconnect(); records.forEach(function (r) { var root = priceRoot(r.target); if (root) { freezeOptions(root); walk(root); } }); ob.observe(doc.documentElement, { subtree: true, childList: true, characterData: true }); });
        ob.observe(doc.documentElement, { subtree: true, childList: true, characterData: true });
      }
    } catch (e) {}
  }
  function frames() { Array.prototype.forEach.call(document.querySelectorAll('iframe'), function (frame) { if (ownedFrame(frame)) { frame.addEventListener('load', function () { convertFrame(frame); }, { once: true }); convertFrame(frame); } }); }
  function run() {
    // After a change between German and Spanish without a reload the settings of the new page apply (same rate).
    var c = read(); if (c) { cfg = c; mid = c.mid_scaled; }
    freezeOptions(document); if (!active()) return; if (observer) observer.disconnect(); walk(document.documentElement); frames(); observe();
  }
  window.ZP_EUR_Currency = { enabled: true, policy: cfg.policy, money: money, amount: amount, eur: eur, schema: schema, edits: edits };
  document.addEventListener('zpl:change', run);
  if (window.MutationObserver) observer = new MutationObserver(queue);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run, { once: true }); else run();
})();
