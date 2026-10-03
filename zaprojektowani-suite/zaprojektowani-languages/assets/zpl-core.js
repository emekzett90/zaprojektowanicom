/*! Zaprojektowani Languages — segmentation core (mirrors includes/Html.php rule-for-rule). */
(function (root, factory) {
  if (typeof module === 'object' && module.exports) { module.exports = factory(); }
  else { root.ZPLCore = factory(); }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';
  var set = function (list) { var o = Object.create(null); list.split(' ').forEach(function (k) { o[k] = 1; }); return o; };
  var SKIP = set('script style textarea noscript code pre svg math iframe object canvas video audio kbd samp head');
  var INLINE = set('a abbr b bdi bdo br cite code data del dfn em font i img ins kbd mark q s samp small span strong sub sup time u var wbr svg picture input');
  var ATOM = set('br wbr img svg picture input code kbd samp math');
  var ATTRS = ['alt', 'title', 'placeholder', 'aria-label', 'aria-placeholder', 'aria-description', 'aria-roledescription', 'aria-valuetext', 'data-zpl-text', 'label'];
  var META = set('description og:title og:description og:image:alt twitter:title twitter:description twitter:image:alt');
  var RX_REMOVE = /[\u00AD\u200B\u200C\u200D\u2060\uFEFF]/g;
  var RX_WS = /[\t\n\u000B\f\r \u00A0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000]+/g;
  var RX_LETTER = /\p{L}/u;
  var RX_NOT_HUMAN = /^(?:https?:)?\/\/|^(?:mailto|tel):|^[\w.+-]+@[\w-]+\.[\w.-]+$|^www\.\S+$|^[#.]?[a-z][\w-]*\{/i;
  var RX_NUMERIC = /^[\d\s.,:;%+\-–—\/()×x*#€$zł]+$/u;
  var RX_MARKER = /<(\/?)(\d+)(\/?)>/g;

  function norm(s) {
    s = String(s).replace(RX_REMOVE, '').replace(RX_WS, ' ');
    return s.replace(/^ +| +$/g, '');
  }
  function human(n) {
    if (!n || n.length > 20000 || !RX_LETTER.test(n)) return false;
    if (RX_NOT_HUMAN.test(n)) return false;
    if (RX_NUMERIC.test(n) && n.indexOf('zł') < 0) return false;
    return true;
  }
  function tagName(el) { return el.localName ? el.localName.toLowerCase() : String(el.nodeName).toLowerCase(); }
  function skipped(el, ignoreTag) {
    var n = tagName(el);
    if (!ignoreTag && SKIP[n]) return true;
    if (!el.hasAttribute) return false;
    if (el.hasAttribute('data-zpl-skip') || el.hasAttribute('data-no-translation')) return true;
    var t = el.getAttribute('translate'); if (t && t.toLowerCase() === 'no') return true;
    var ce = el.getAttribute('contenteditable'); if (ce !== null && ce.toLowerCase() !== 'false') return true;
    if (el.id === 'wpadminbar') return true;
    var c = el.getAttribute('class');
    if (c && /(?:^|\s)(?:notranslate|zpl-skip|zpl-switch)(?:\s|$)/.test(c)) return true;
    return false;
  }
  function textOf(node) { return norm(node.nodeValue || ''); }
  function childList(el) {
    return tagName(el) === 'template' && el.content ? el.content.childNodes : el.childNodes;
  }
  function inlineOnly(el) {
    var c = childList(el);
    for (var i = 0; i < c.length; i++) {
      var ch = c[i];
      if (ch.nodeType !== 1) continue;
      var n = tagName(ch);
      if (!INLINE[n]) return false;
      if (ATOM[n] || skipped(ch)) continue;
      if (!inlineOnly(ch)) return false;
    }
    return true;
  }
  function directText(el) {
    var c = childList(el);
    for (var i = 0; i < c.length; i++) { if (c[i].nodeType === 3 && textOf(c[i]) !== '') return true; }
    return false;
  }
  function hasText(el) {
    var c = el.childNodes;
    for (var i = 0; i < c.length; i++) {
      var ch = c[i];
      if (ch.nodeType === 3) { if (textOf(ch) !== '') return true; }
      else if (ch.nodeType === 1 && !ATOM[tagName(ch)] && !skipped(ch) && hasText(ch)) return true;
    }
    return false;
  }
  function escKey(t) { return t.replace(/&/g, '&amp;').replace(/</g, '&lt;'); }
  function keyOf(el, els, ctr) {
    var out = '', c = el.childNodes;
    for (var i = 0; i < c.length; i++) {
      var ch = c[i];
      if (ch.nodeType === 3) { out += escKey(ch.nodeValue); continue; }
      if (ch.nodeType !== 1) continue;
      var n = ++ctr.n; els[n] = ch;
      if (ATOM[tagName(ch)] || skipped(ch) || !hasText(ch)) { out += '<' + n + '/>'; continue; }
      out += '<' + n + '>' + keyOf(ch, els, ctr) + '</' + n + '>';
    }
    return out;
  }
  function collectTexts(el, texts, onAttr) {
    var c = el.childNodes;
    for (var i = 0; i < c.length; i++) {
      var ch = c[i];
      if (ch.nodeType === 3) { if (textOf(ch) !== '') texts.push(ch); continue; }
      if (ch.nodeType !== 1) continue;
      if (skipped(ch)) { if ((tagName(ch) === 'textarea' || tagName(ch) === 'iframe') && !skipped(ch, true)) onAttr(ch); continue; }
      onAttr(ch);
      if (ATOM[tagName(ch)]) { atomAttrs(ch, onAttr); continue; }
      collectTexts(ch, texts, onAttr);
    }
  }
  function atomAttrs(el, onAttr) {
    var c = el.children || [];
    for (var i = 0; i < c.length; i++) { if (SKIP[tagName(c[i])]) continue; onAttr(c[i]); atomAttrs(c[i], onAttr); }
  }
  function segment(rootEl, visit, onAttr) {
    var texts = [];
    collectTexts(rootEl, texts, onAttr);
    if (!texts.length) return;
    if (texts.length === 1) {
      var k = textOf(texts[0]);
      if (human(k)) visit({ k: k, type: 'text', node: texts[0] });
      return;
    }
    var els = {}, key = norm(keyOf(rootEl, els, { n: 0 }));
    var plain = norm(key.replace(/<\/?\d+\/?>/g, ' ')).replace(/&lt;/g, '<').replace(/&amp;/g, '&');
    if (!human(plain)) return;
    visit({ k: key, type: 'multi', root: rootEl, els: els });
  }
  function bare(node, visit) {
    var k = textOf(node);
    if (k !== '' && human(k)) visit({ k: k, type: 'text', node: node });
  }
  function element(el, visit, onAttr) {
    if (tagName(el) === 'script') return;
    // Textarea/iframe content is not page text, but their placeholder/title/aria-label are UI text.
    if (tagName(el) === 'textarea' || tagName(el) === 'iframe') { if (!skipped(el, true)) onAttr(el); return; }
    if (skipped(el)) return;
    onAttr(el);
    if (tagName(el) === 'template') { walkChildren(el, visit, onAttr); return; }
    if (inlineOnly(el) && directText(el)) { segment(el, visit, onAttr); return; }
    walkChildren(el, visit, onAttr);
  }
  function walkChildren(el, visit, onAttr) {
    var c = Array.prototype.slice.call(childList(el));
    for (var i = 0; i < c.length; i++) {
      if (c[i].nodeType === 3) bare(c[i], visit);
      else if (c[i].nodeType === 1) element(c[i], visit, onAttr);
    }
  }
  /** Walk a subtree root exactly as the server walks <body>. */
  function walk(rootEl, visit, onAttr) {
    onAttr = onAttr || function () {};
    if (rootEl.nodeType === 3) { bare(rootEl, visit); return; }
    if (tagName(rootEl) === 'body') { onAttr(rootEl); walkChildren(rootEl, visit, onAttr); return; }
    element(rootEl, visit, onAttr);
  }
  /** Translatable attribute items of an element: [attr, value]. */
  function attrItems(el) {
    var out = [], n = tagName(el), v;
    if (n === 'meta') {
      var key = (el.getAttribute('name') || el.getAttribute('property') || '').toLowerCase();
      if (META[key] && el.hasAttribute('content')) { v = norm(el.getAttribute('content')); if (human(v)) out.push(['content', v]); }
      return out;
    }
    for (var i = 0; i < ATTRS.length; i++) {
      if (!el.hasAttribute(ATTRS[i])) continue;
      v = norm(el.getAttribute(ATTRS[i])); if (human(v)) out.push([ATTRS[i], v]);
    }
    if (n === 'input' && el.hasAttribute('value')) {
      var type = (el.getAttribute('type') || 'text').toLowerCase();
      if (type === 'submit' || type === 'button' || type === 'reset') { v = norm(el.getAttribute('value')); if (human(v)) out.push(['value', v]); }
    }
    return out;
  }
  function unesc(t) { return t.indexOf('&') < 0 ? t : t.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#0*39;|&apos;/g, "'").replace(/&amp;/g, '&'); }
  /** Tokens of a marker string, or null if malformed. */
  function tokens(s) {
    var out = [], stack = [], pos = 0, m;
    RX_MARKER.lastIndex = 0;
    while ((m = RX_MARKER.exec(s))) {
      if (m.index > pos) out.push(['x', unesc(s.slice(pos, m.index))]);
      var n = +m[2];
      if (m[1] === '/') { if (stack.pop() !== n) return null; out.push(['c', n]); }
      else if (m[3] === '/') out.push(['a', n]);
      else { stack.push(n); out.push(['o', n]); }
      pos = m.index + m[0].length;
    }
    if (pos < s.length) out.push(['x', unesc(s.slice(pos))]);
    return stack.length ? null : out;
  }
  function markersMatch(a, b) {
    var x = (a.match(/<\/?\d+\/?>/g) || []).sort().join(), y = (b.match(/<\/?\d+\/?>/g) || []).sort().join();
    return x === y && tokens(b) !== null;
  }
  /** Numbers in text parts -> {1},{2}... Returns [template, values] or null when the key has no numbers. */
  var RX_NUM = /\d+(?:[.,:\u00A0\u202F ]\d+)*/g;
  function template(key) {
    var vals = [], parts = key.split(/(<\/?\d+\/?>)/);
    for (var i = 0; i < parts.length; i += 2) {
      parts[i] = parts[i].replace(RX_NUM, function (m) { vals.push(m); return '{' + vals.length + '}'; });
    }
    return vals.length ? [parts.join(''), vals] : null;
  }
  function num(v, lang) {
    if (lang === 'en') {
      if (/^\d{1,3}(?:[ \u00A0\u202F]\d{3})+$/.test(v)) return v.replace(/[ \u00A0\u202F]/g, ',');
      if (/^\d+,\d{1,2}$/.test(v)) return v.replace(',', '.');
    } else {
      if (/^\d{1,3}(?:,\d{3})+$/.test(v)) return v.replace(/,/g, '\u00A0');
      if (/^\d+\.\d{1,2}$/.test(v)) return v.replace('.', ',');
    }
    return v;
  }
  function fill(tr, vals, lang) { return tr.replace(/\{(\d+)\}/g, function (m, i) { return vals[i - 1] !== undefined ? num(vals[i - 1], lang || 'en') : m; }); }
  /** Extra data-* attributes shown on the page by scripts/CSS (set by Skaner EN, mirrors Html::$extra). */
  function addAttrs(list) {
    (list || []).forEach(function (a) { a = String(a); if (/^data-[a-z0-9-]{1,40}$/.test(a) && ATTRS.indexOf(a) < 0) ATTRS.push(a); });
    return ATTRS.slice();
  }
  return { addAttrs: addAttrs, norm: norm, human: human, walk: walk, attrItems: attrItems, tokens: tokens, markersMatch: markersMatch, skipped: skipped, tagName: tagName, template: template, fill: fill, META: META };
}));
