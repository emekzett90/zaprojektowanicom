(function () {
  'use strict';
  if (window.__zpStudioCompanyLookup) return;
  window.__zpStudioCompanyLookup = true;
  var config = window.zpStudioCompanyLookup || {};
  var copy = {
    pl: {
      nip: 'NIP firmy (opcjonalnie)', check: 'Pobierz dane firmy', loading: 'Sprawdzamy…',
      help: 'Podaj NIP, aby pobrać dostępne dane firmy. Pole jest opcjonalne.',
      registry: 'Sprawdzimy dane w MF, a w razie braku wyniku — w REGON przez skanfirmy.pl.',
      registryCeidg: 'Sprawdzimy dane w CEIDG lub MF, a w razie braku wyniku — w REGON przez skanfirmy.pl.',
      address: 'Adres firmy (opcjonalnie)', regon: 'REGON (opcjonalnie)', use: 'Użyj tych danych',
      replace: 'Przycisk wstawi tę nazwę, adres i REGON do pól firmy. Możesz je później zmienić.',
      missingAddress: 'Rejestr nie podał adresu — możesz wpisać go ręcznie.',
      invalid_nip: 'Sprawdź NIP: powinien zawierać 10 cyfr i poprawną cyfrę kontrolną. Dane możesz też wpisać ręcznie.',
      not_found: 'Nie znaleziono danych w dostępnym rejestrze. To nie oznacza, że firma nie istnieje. Wpisz dane ręcznie lub kontynuuj bez NIP.',
      unavailable: 'Rejestr jest teraz niedostępny. Wpisz dane ręcznie lub spróbuj później — możesz nadal wysłać zapytanie.',
      restricted: 'Rejestr nie udostępnia tych danych. Wpisz dane ręcznie — możesz nadal wysłać zapytanie.',
      expired: 'Odśwież stronę, aby ponowić pobieranie danych. Możesz również wpisać dane ręcznie i wysłać zapytanie.',
      limited: 'Chwilowo osiągnięto limit sprawdzeń. Spróbuj później albo wpisz dane ręcznie.',
      busy: 'Trwa już sprawdzanie tego NIP. Spróbuj za chwilę albo wpisz dane ręcznie.',
      found: 'Znaleźliśmy dane. Sprawdź je i wybierz „Użyj tych danych”, aby uzupełnić pola.',
      foundPartial: 'Znaleziono nazwę firmy. Adres i REGON uzupełnij ręcznie.',
      replacePartial: 'Przycisk wstawi nazwę firmy. Ręcznie wpisany adres i REGON pozostaną bez zmian.',
      applied: 'Dane firmy zostały wstawione. Sprawdź je przed wysłaniem zapytania.',
      appliedPartial: 'Nazwa firmy została wstawiona. Sprawdź adres i REGON lub uzupełnij je ręcznie.',
      edited: 'Dane zostały zmienione ręcznie. Możesz kontynuować zapytanie.',
      asOf: 'Stan na', source: 'Źródło danych firmy', summaryNip: 'NIP', summaryAddress: 'Adres firmy',
      summaryRegon: 'REGON', ceidg: 'CEIDG', mfShort: 'Wykaz podatników VAT (MF)',
      skanfirmy: 'REGON przez skanfirmy.pl', city: 'Miejscowość', checkedOn: 'Sprawdzono'
    },
    en: {
      nip: 'Polish Tax ID / NIP (optional)', check: 'Find company details', loading: 'Checking…',
      help: 'No company or Polish NIP? Skip this field. You can also enter the details manually.',
      registry: 'At your request, we will check a public company register. You can edit the details before sending.',
      mf: 'Source: Poland’s VAT taxpayer register (Ministry of Finance). It does not cover every business.',
      address: 'Company address (optional)', regon: 'REGON (optional)', use: 'Use these details',
      replace: 'The button will insert this name, address and REGON into the company fields. You can edit them afterwards.',
      missingAddress: 'The register did not provide an address — you can enter it manually.',
      invalid_nip: 'Check the NIP: it should contain 10 digits and a valid check digit. You can also enter the details manually.',
      not_found: 'No details were found in the available register. This does not mean the company does not exist. Enter the details manually or continue without a NIP.',
      unavailable: 'The register is currently unavailable. Enter the details manually or try later — you can still send your enquiry.',
      expired: 'Refresh the page to try the lookup again. You can also enter the details manually and send your enquiry.',
      limited: 'The lookup limit has temporarily been reached. Try later or enter the details manually.',
      busy: 'This NIP is already being checked. Try again shortly or enter the details manually.',
      found: 'We found company details. Review them and select “Use these details” to fill in the fields.',
      applied: 'The company details have been inserted. Check them before sending your enquiry.',
      edited: 'The details were edited manually. You can continue with your enquiry.',
      asOf: 'As of', source: 'Company data source', summaryNip: 'NIP', summaryAddress: 'Company address',
      summaryRegon: 'REGON', ceidg: 'CEIDG', mfShort: 'VAT taxpayer register (MF)'
    }
  };
  var root, panel, fields = {}, labels = {}, status, preview, previewName, previewText, check, use;
  var language = '', statusCode = '', pending = null, candidate = null, appliedRecord = null, seq = 0, applying = false, scheduled = false;
  var keys = ['nip', 'companyAddress', 'regon', 'companySource'];

  function el(tag, className, text) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (text) node.textContent = text;
    return node;
  }
  function setText(node, text) { if (node && node.textContent !== text) node.textContent = text; }
  function lang() {
    var value = document.documentElement.lang || 'pl';
    try { if (window.ZPL && typeof window.ZPL.lang === 'function') value = window.ZPL.lang(); } catch (e) {}
    return /^en(?:-|$)/i.test(value) ? 'en' : 'pl';
  }
  function brand() { return !!root && root.getAttribute('data-service') === 'brand' && lang() === 'pl'; }
  function nip(value) {
    var n = String(value || '').trim().replace(/^PL\s*/i, '').replace(/[\s-]+/g, '');
    if (!/^[0-9]{10}$/.test(n) || /^([0-9])\1{9}$/.test(n)) return '';
    var weights = [6, 5, 7, 2, 3, 4, 5, 6, 7], sum = 0;
    weights.forEach(function (w, i) { sum += Number(n.charAt(i)) * w; });
    return sum % 11 < 10 && sum % 11 === Number(n.charAt(9)) ? n : '';
  }
  function source(record) { return record.source === 'ceidg' ? copy[language].ceidg : (record.source === 'skanfirmy-regon' ? copy[language].skanfirmy : copy[language].mfShort); }
  function date(record) { return /^\d{4}-\d{2}-\d{2}$/.test(record.asOf || '') ? record.asOf : ''; }
  function setStatus(code) {
    statusCode = code || '';
    setText(status, copy[language][statusCode] || '');
    if (status) status.setAttribute('data-error', /^(invalid_nip|unavailable|restricted|expired|limited|busy)$/.test(statusCode) ? 'true' : 'false');
  }
  function cancel() {
    seq += 1;
    if (pending) pending.abort();
    pending = null; candidate = null;
    if (preview) preview.hidden = true;
    if (check) { check.disabled = false; setText(check, copy[language || lang()].check); }
    if (panel) panel.removeAttribute('aria-busy');
  }
  function removePolishFields() {
    cancel();
    keys.forEach(function (key) {
      if (!fields[key]) return;
      fields[key].value = ''; fields[key].disabled = true; fields[key].removeAttribute('data-field');
    });
    if (root) root.querySelectorAll('[data-zp-company-summary]').forEach(function (row) { row.remove(); });
    if (panel) panel.remove();
    panel = null; fields = {}; labels = {}; status = null; preview = null; previewName = null; previewText = null; check = null; use = null;
    language = ''; statusCode = ''; appliedRecord = null;
  }
  function field(key, id, autocomplete) {
    var label = el('label', 'zpbsField'), span = el('span'), input = el('input');
    label.htmlFor = id; input.id = id; input.type = 'text'; input.autocomplete = autocomplete || 'off';
    input.setAttribute('data-zp-company-field', key);
    if (key === 'nip' || key === 'regon') input.inputMode = 'numeric';
    if (key === 'nip') input.maxLength = 24;
    if (key === 'regon') input.maxLength = 14;
    fields[key] = input; labels[key] = span; label.appendChild(span); label.appendChild(input);
    return label;
  }
  function mount(company) {
    panel = el('div', 'zpStudioCompany'); panel.setAttribute('data-zp-studio-company', '');
    panel.setAttribute('data-zpl-skip', ''); panel.setAttribute('translate', 'no');
    var row = el('div', 'zpStudioCompany__row');
    row.appendChild(field('nip', 'zpStudioCompanyNip'));
    check = el('button', 'zpStudioCompany__button'); check.type = 'button';
    check.setAttribute('data-zp-company-check', ''); row.appendChild(check); panel.appendChild(row);
    var help = el('p', 'zpStudioCompany__help'); help.id = 'zpStudioCompanyHelp'; labels.help = help;
    fields.nip.setAttribute('aria-describedby', 'zpStudioCompanyHelp zpStudioCompanyStatus'); panel.appendChild(help);
    var registry = el('p', 'zpStudioCompany__help'); labels.registry = registry; panel.appendChild(registry);
    status = el('p', 'zpStudioCompany__status'); status.id = 'zpStudioCompanyStatus';
    status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite'); status.setAttribute('aria-atomic', 'true'); panel.appendChild(status);
    preview = el('div', 'zpStudioCompany__preview'); preview.hidden = true;
    previewName = el('strong'); previewText = el('p'); preview.appendChild(previewName); preview.appendChild(previewText);
    var replace = el('p'); labels.replace = replace; preview.appendChild(replace);
    use = el('button', 'zpStudioCompany__button'); use.type = 'button'; use.setAttribute('data-zp-company-use', ''); preview.appendChild(use); panel.appendChild(preview);
    var details = el('div', 'zpStudioCompany__fields');
    details.appendChild(field('companyAddress', 'zpStudioCompanyAddress', 'street-address'));
    details.appendChild(field('regon', 'zpStudioCompanyRegon')); panel.appendChild(details);
    fields.companySource = el('input'); fields.companySource.type = 'hidden'; fields.companySource.setAttribute('data-zp-company-field', 'companySource'); panel.appendChild(fields.companySource);
    company.closest('label').insertAdjacentElement('afterend', panel);
    check.addEventListener('click', lookup);
    fields.nip.addEventListener('keydown', function (event) { if (event.key === 'Enter') { event.preventDefault(); event.stopPropagation(); lookup(); } });
    use.addEventListener('click', applyCandidate);
    language = '';
  }
  function translate() {
    var next = lang();
    if (next === language) return;
    language = next; panel.lang = language;
    var c = copy[language];
    setText(labels.nip, c.nip); setText(labels.companyAddress, c.address); setText(labels.regon, c.regon);
    setText(labels.help, c.help); setText(labels.registry, /^ceidg-/.test(config.provider || '') ? c.registryCeidg : c.registry);
    setText(labels.replace, c.replace); setText(check, pending ? c.loading : c.check); setText(use, c.use);
    if (appliedRecord && fields.companySource.value) fields.companySource.value = source(appliedRecord) + (date(appliedRecord) ? ' · ' + date(appliedRecord) : '');
    setStatus(statusCode);
    if (candidate) showCandidate();
  }
  function showCandidate() {
    if (!candidate) return;
    setText(previewName, candidate.name);
    var parts = [(!candidate.partial && candidate.address) || copy[language].missingAddress];
    if (candidate.source === 'skanfirmy-regon' && candidate.city) parts.push(copy[language].city + ': ' + candidate.city);
    if (!candidate.partial && candidate.regon) parts.push('REGON: ' + candidate.regon);
    parts.push(source(candidate) + (date(candidate) ? ' · ' + (candidate.source === 'skanfirmy-regon' ? copy[language].checkedOn : copy[language].asOf) + ' ' + date(candidate) : ''));
    setText(labels.replace, candidate.partial ? copy[language].replacePartial : copy[language].replace);
    setText(previewText, parts.join(' · ')); preview.hidden = false;
  }
  function lookup() {
    if (!brand() || pending) return;
    var number = nip(fields.nip.value);
    cancel(); fields.nip.removeAttribute('aria-invalid');
    if (!number) { fields.nip.setAttribute('aria-invalid', 'true'); setStatus('invalid_nip'); fields.nip.focus(); return; }
    fields.nip.value = number;
    if (!config.ajax || !config.nonce || !window.fetch || !window.AbortController) { setStatus('unavailable'); return; }
    var request = ++seq, controller = new AbortController(), timer;
    pending = controller; check.disabled = true; setText(check, copy[language].loading); panel.setAttribute('aria-busy', 'true'); setStatus('');
    var body = new URLSearchParams(); body.set('action', 'zp_studio_company_lookup'); body.set('nonce', config.nonce); body.set('nip', number);
    timer = window.setTimeout(function () { controller.abort(); }, 15000);
    fetch(config.ajax, { method: 'POST', credentials: 'same-origin', body: body, signal: controller.signal })
      .then(function (response) { return response.json(); })
      .then(function (response) {
        if (request !== seq || !brand() || nip(fields.nip.value) !== number) return;
        var data = response && response.data;
        if (!response || !response.success) { setStatus(data && copy[language][data.code] ? data.code : 'unavailable'); return; }
        if (!data || data.status !== 'found' || !data.record || data.record.nip !== number || typeof data.record.name !== 'string' || !data.record.name) { setStatus('not_found'); return; }
        candidate = data.record; showCandidate(); setStatus(candidate.partial ? 'foundPartial' : 'found');
      })
      .catch(function () { if (request === seq && brand()) setStatus('unavailable'); })
      .finally(function () {
        window.clearTimeout(timer);
        if (request === seq) { pending = null; check.disabled = false; setText(check, copy[language].check); panel.removeAttribute('aria-busy'); }
      });
  }
  function emit(input) { input.dispatchEvent(new Event('input', { bubbles: true })); input.dispatchEvent(new Event('change', { bubbles: true })); }
  function applyCandidate() {
    if (!brand() || !candidate || candidate.nip !== nip(fields.nip.value)) return;
    var company = root.querySelector('[data-field="company"]');
    if (!company) return;
    applying = true;
    company.value = candidate.name;
    // Missing registry data never erases a manually supplied address or REGON.
    if (!candidate.partial && candidate.address) fields.companyAddress.value = candidate.address;
    if (!candidate.partial && candidate.regon) fields.regon.value = candidate.regon;
    fields.companySource.value = source(candidate) + (date(candidate) ? ' · ' + date(candidate) : '');
    [company, fields.nip, fields.companyAddress, fields.regon, fields.companySource].forEach(emit);
    appliedRecord = candidate;
    applying = false; preview.hidden = true; candidate = null; setStatus(appliedRecord.partial ? 'appliedPartial' : 'applied'); syncSummary();
  }
  function syncSummary() {
    if (!root || !panel) return;
    var container = root.querySelector('[data-final-summary]');
    if (!container) return;
    if (!brand()) { container.querySelectorAll('[data-zp-company-summary]').forEach(function (row) { row.remove(); }); return; }
    var c = copy[language], names = { nip: c.summaryNip, companyAddress: c.summaryAddress, regon: c.summaryRegon, companySource: c.source };
    keys.forEach(function (key) {
      var row = container.querySelector('[data-zp-company-summary="' + key + '"]'), value = fields[key].value.trim();
      if (!value) { if (row) row.remove(); return; }
      if (!row) { row = el('div', 'zpbsFinalRow'); row.setAttribute('data-zp-company-summary', key); row.setAttribute('data-zpl-skip', ''); row.setAttribute('translate', 'no'); row.appendChild(el('span')); row.appendChild(el('strong')); container.appendChild(row); }
      setText(row.firstElementChild, names[key]); setText(row.lastElementChild, value);
    });
  }
  function refresh() {
    scheduled = false;
    if (!root) return;
    if (lang() !== 'pl') { removePolishFields(); return; }
    var company = root.querySelector('[data-slide="4"] [data-field="company"]');
    if (!company || !company.closest('label')) return;
    if (!panel || !panel.isConnected) mount(company);
    translate();
    var active = brand();
    if (panel.hidden === active) panel.hidden = !active;
    if (!active && (pending || candidate)) cancel();
    keys.forEach(function (key) {
      fields[key].disabled = !active;
      if (active) { if (fields[key].getAttribute('data-field') !== key) fields[key].setAttribute('data-field', key); }
      else fields[key].removeAttribute('data-field');
    });
    syncSummary();
  }
  function schedule() { if (!scheduled) { scheduled = true; window.setTimeout(refresh, 0); } }
  function init() {
    root = document.getElementById('zpbsUltimate');
    if (!root) return;
    refresh();
    root.addEventListener('input', function (event) {
      if (!panel || applying) return;
      if (event.target === fields.nip) { cancel(); fields.nip.removeAttribute('aria-invalid'); setStatus(''); }
      if (event.target.matches('[data-field="company"],[data-zp-company-field="nip"],[data-zp-company-field="companyAddress"],[data-zp-company-field="regon"]')) {
        if (fields.companySource.value) { fields.companySource.value = ''; appliedRecord = null; setStatus('edited'); }
        syncSummary();
      }
    });
    new MutationObserver(schedule).observe(root, { subtree: true, childList: true, attributes: true, attributeFilter: ['data-service', 'data-step'] });
    document.addEventListener('zpl:change', function () { if (lang() !== 'pl') removePolishFields(); schedule(); });
    new MutationObserver(schedule).observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
  }
  // The active Studio step/final-submit handlers call this before advancing or sending.
  // It does not depend on registry availability and never sends a lead itself.
  window.zpStudioCompanyValidate = function () {
    if (lang() !== 'pl') { removePolishFields(); return true; }
    if (!brand() || !fields.nip || !fields.nip.value.trim()) return true;
    if (nip(fields.nip.value)) { fields.nip.removeAttribute('aria-invalid'); return true; }
    fields.nip.setAttribute('aria-invalid', 'true'); setStatus('invalid_nip');
    if (root.getAttribute('data-step') === '4') {
      fields.nip.focus();
      // The existing step observer focuses its heading after a timeline jump.
      window.setTimeout(function () {
        if (brand() && root.getAttribute('data-step') === '4' && fields.nip && fields.nip.isConnected && fields.nip.value.trim() && !nip(fields.nip.value)) fields.nip.focus();
      }, 0);
    }
    return false;
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true }); else init();
})();
