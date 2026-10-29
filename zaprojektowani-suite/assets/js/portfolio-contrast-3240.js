/* Sample the already decoded image under the category pill. No downloads, scroll loop or dependencies. */
(() => {
  'use strict';
  const cardSelector = '.zpShowcaseWhite__card, .zpShowcaseMobile__card';
  const pillSelector = '.zpShowcaseWhite__pill, [data-zp-project-type]';
  const states = new WeakMap();
  const cards = new Set();
  let canvas, ctx;
  let timer;
  let lastWidth = window.innerWidth;
  const linear = value => {
    const s = value / 255;
    return s <= .04045 ? s / 12.92 : Math.pow((s + .055) / 1.055, 2.4);
  };
  const percent = (value, fallback) => value && value.endsWith('%') ? parseFloat(value) / 100 : fallback;
  function sample(card) {
    const state = states.get(card);
    if (!state || !card.isConnected || !state.image.complete || state.image.naturalWidth < 40) return;
    const image = state.image;
    const ir = image.getBoundingClientRect();
    const pr = state.pill.getBoundingClientRect();
    if (!ir.width || !ir.height || !pr.width || !pr.height) return;
    // The same URL may be cropped differently on mobile or after a breakpoint change.
    const key = [image.currentSrc || image.src, Math.round(ir.width), Math.round(ir.height), Math.round(pr.width), Math.round(pr.height)].join('|');
    if (key === state.key) return;
    state.key = key;
    try {
      if (!canvas) {
        canvas = document.createElement('canvas');canvas.width = 40;canvas.height = 12;
        ctx = canvas.getContext('2d', { willReadFrequently: true });
      }
      if (!ctx) return;
      const css = getComputedStyle(image);
      const iw = image.naturalWidth, ih = image.naturalHeight;
      const fit = css.objectFit;
      let sx = ir.width / iw, sy = ir.height / ih;
      if (fit === 'cover') sx = sy = Math.max(sx, sy);
      else if (fit === 'contain' || fit === 'scale-down') sx = sy = Math.min(sx, sy);
      const pos = css.objectPosition.split(/\s+/);
      const ox = (ir.width - iw * sx) * percent(pos[0], .5);
      const oy = (ir.height - ih * sy) * percent(pos[1], .5);
      let x = (pr.left - ir.left - ox) / sx;
      let y = (pr.top - ir.top - oy) / sy;
      x = Math.max(0, Math.min(iw - 1, x));y = Math.max(0, Math.min(ih - 1, y));
      const w = Math.max(1, Math.min(iw - x, pr.width / sx));
      const h = Math.max(1, Math.min(ih - y, pr.height / sy));
      ctx.clearRect(0, 0, 40, 12);
      ctx.drawImage(image, x, y, w, h, 0, 0, 40, 12);
      const pixels = ctx.getImageData(0, 0, 40, 12).data;
      let luminance = 0, count = 0;
      for (let i = 0; i < pixels.length; i += 4) {
        const alpha = pixels[i + 3] / 255;
        // Transparent project art sits on the card's dark base.
        luminance += .2126 * linear(pixels[i] * alpha + 7 * (1-alpha)) + .7152 * linear(pixels[i+1] * alpha + 20 * (1-alpha)) + .0722 * linear(pixels[i+2] * alpha + 38 * (1-alpha));
        count++;
      }
      card.dataset.zpChipTone = luminance / Math.max(1,count) >= .24 ? 'light' : 'dark';
      card.dataset.zpChipSample = 'image';
    } catch (_) {
      // A third-party CDN may prohibit pixel reads. Keep the opaque, high-contrast light pill.
      // Do not retry with another HTTP request and do not change img.crossOrigin after load.
      card.dataset.zpChipTone = 'light';card.dataset.zpChipSample = 'fallback';
      canvas = null;ctx = null;
    }
  }
  const schedule = card => {
    const state = states.get(card);
    if (!state || state.pending) return;
    state.pending = true;
    const run = () => { state.pending = false; sample(card); };
    if ('requestIdleCallback' in window) window.requestIdleCallback(run, { timeout: 600 });
    else window.setTimeout(run, 30);
  };
  const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
    entries.forEach(entry => { if (entry.isIntersecting) schedule(entry.target); });
  }, { rootMargin: '180px', threshold: 0 }) : null;
  function register(card) {
    if (states.has(card)) return;
    const image = card.querySelector('img.zpShowcaseWhite__img, img.zpShowcaseMobile__img');
    const pill = card.querySelector(pillSelector);
    if (!image || !pill) return;
    states.set(card, { image, pill, key: '', pending: false });cards.add(card);
    image.addEventListener('load', () => schedule(card));
    if (observer) observer.observe(card);else schedule(card);
    if (image.complete && image.naturalWidth > 40) schedule(card);
  }
  function scan(root) {
    if (root.nodeType !== 1) return;
    if (root.matches(cardSelector)) register(root);
    root.querySelectorAll(cardSelector).forEach(register);
  }
  function init() {
    // Only observe the two portfolio components, never all changes to the document.
    const roots = new Set(document.querySelectorAll('#zpShowcaseWhite, .zpShowcaseMobile'));
    roots.forEach(root => {
      scan(root);
      new MutationObserver(mutations => {
        mutations.forEach(m => m.addedNodes.forEach(scan));
        cards.forEach(card => { if (!card.isConnected) { observer?.unobserve(card); cards.delete(card); } });
      }).observe(root, { childList: true, subtree: true });
    });
  }
  window.addEventListener('resize', () => {
    // iOS URL-bar height changes must not cause work or a relayout.
    if (window.innerWidth === lastWidth) return;
    lastWidth = window.innerWidth;window.clearTimeout(timer);
    timer = window.setTimeout(() => cards.forEach(schedule), 150);
  }, { passive: true });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once:true });else init();
})();
