/* Progressive links for the existing, independently rendered project dialogs. */
(() => {
  'use strict';
  const config = window.ZP_CASE_STUDIES_20261015;
  if (!config || !config.links) return;
  const canonicalBrand = value => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim().toLowerCase();
  const keyFor = value => ({ siemianowski: 'siemianowski', 'apartament piekna': 'apartament' })[canonicalBrand(value)] || '';
  const safeLink = key => {
    try {
      const url = new URL(config.links[key], location.origin);
      return url.origin === location.origin && /^https?:$/.test(url.protocol) ? url.href : '';
    } catch (_) { return ''; }
  };
  const fixtures = [
    { root: '#zpShowcasePop', title: '#zpShowcasePopTitle', footer: '.zpShowcasePop__cta' },
    { root: '#projectDialog', title: '#projectDialogTitle, #dialogTitle', footer: '.project-dialog__actions, .dialog-cta' },
    { root: '.modal[data-modal]', title: '#sheetTitle', footer: '.introActions' },
  ];
  for (const fixture of fixtures) {
    for (const root of document.querySelectorAll(fixture.root)) {
      const sync = () => {
        const title = root.querySelector(fixture.title);
        const key = keyFor(title?.textContent);
        const href = key ? safeLink(key) : '';
        let link = root.querySelector('[data-zp-full-case]');
        if (!href) { link?.remove(); return; }
        const footer = root.querySelector(fixture.footer);
        if (!footer) return;
        if (!link) {
          link = document.createElement('a');
          link.className = 'zpFullCaseLink';
          link.dataset.zpFullCase = '1';
          footer.append(link);
        }
        // Avoid mutation loops: observer sees our insert once, then no write is needed.
        if (link.href !== href) link.href = href;
        if (link.textContent !== config.label) link.textContent = config.label;
      };
      sync();
      new MutationObserver(sync).observe(root, { childList: true, subtree: true, characterData: true });
    }
  }
})();
