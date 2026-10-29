/* Local, keyboard-accessible language demonstration. No fetch, reload or site-wide language change. */
(() => {
  'use strict';
  const init = () => document.querySelectorAll('[data-zp-language-demo]').forEach(root => {
    if (root.dataset.zpDemoReady) return;
    const preview = root.querySelector('.zpLangDemo__preview');
    const buttons = [...root.querySelectorAll('[data-zp-demo-language]')];
    if (!preview || buttons.length !== 3) return;
    root.dataset.zpDemoReady = '1';
    buttons.forEach(button => {
      const template = root.querySelector('template[data-zp-demo-template="' + button.dataset.zpDemoLanguage + '"]');
      if (!template) return;
      button.disabled = false;
      button.addEventListener('click', () => {
        if (button.getAttribute('aria-pressed') === 'true') return;
        buttons.forEach(other => other.setAttribute('aria-pressed', String(other === button)));
        preview.replaceChildren(template.content.cloneNode(true));
        root.dataset.zpLanguageSelected = button.dataset.zpDemoLanguage;
      });
    });
  });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
  else init();
})();
