(() => {
  'use strict';
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const init = () => document.querySelectorAll('[data-zp-coverage]').forEach(root => {
    if (root.dataset.coverageReady) return;
    root.dataset.coverageReady = '1';
    const buttons = [...root.querySelectorAll('[data-cov-city]')];
    const status = root.querySelector('[data-cov-status]');
    if (!buttons.length || !status) return;
    buttons.forEach(button => button.addEventListener('click', () => {
      if (button.getAttribute('aria-pressed') === 'true') return;
      buttons.forEach(other => other.setAttribute('aria-pressed', String(other === button)));
      root.querySelectorAll('[data-cov-dot]').forEach(dot => { dot.dataset.active = String(dot.dataset.covDot === button.dataset.covCity); });
      status.querySelector('[data-cov-name]').textContent = button.dataset.name;
      status.querySelector('[data-cov-mode]').textContent = button.dataset.mode;
      status.querySelector('[data-cov-copy]').textContent = button.dataset.copy;
    }));
    if (!motion.matches && 'IntersectionObserver' in window) {
      root.dataset.coverageMotion = 'ready';
      const observer = new IntersectionObserver(entries => {
        if (!entries.some(entry => entry.isIntersecting)) return;
        root.dataset.coverageMotion = 'entered';
        observer.disconnect();
      }, { rootMargin: '80px 0px', threshold: .05 });
      observer.observe(root);
    }
    motion.addEventListener?.('change', event => {
      if (event.matches) delete root.dataset.coverageMotion;
    });
  });
  init();
  // 3.2.97: a language change without reloading (zaprojektowani-languages) brings the section back when coming
  // back to Polish: it is prepared the same way.
  document.addEventListener('zpl:change', init);
})();
