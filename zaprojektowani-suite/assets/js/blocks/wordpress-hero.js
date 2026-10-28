(function () {
  'use strict';
  function init() {
    document.querySelectorAll('[data-zp-wp-tech]').forEach(function (strip) {
      if (strip.classList.contains('is-ready')) return;
      var button = strip.querySelector('.zpWpTech__pause');
      if (!button) return;
      strip.classList.add('is-ready');
      button.addEventListener('click', function () {
        var paused = strip.classList.toggle('is-paused');
        button.setAttribute('aria-pressed', paused ? 'true' : 'false');
        button.setAttribute('aria-label', paused ? 'Wznów przewijanie logotypów' : 'Zatrzymaj przewijanie logotypów');
      });
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
