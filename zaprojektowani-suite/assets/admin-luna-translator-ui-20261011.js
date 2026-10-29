(function () {
  'use strict';
  var root = document.getElementById('zpLunaUI'), c = window.zpLunaUIConfig;
  if (!root || !c) return;
  var url;
  try { url = new URL(c.url, location.href); if (url.origin !== location.origin || !/\/wp-admin\/admin-ajax\.php$/.test(url.pathname) || url.username || url.password) return; } catch (_) { return; }
  var live = document.getElementById('zpLunaLive'), rows = document.getElementById('zpLunaRows'), search = document.getElementById('zpLunaSearch'), filter = document.getElementById('zpLunaFilter'), calls = 0, stopped = false;
  function number(n) { return Number.isSafeInteger(n) && n >= 0 ? n : null; }
  function money(n) { n = number(n); return n === null ? '—' : (n / 1e6).toFixed(3) + ' USD'; }
  function text(id, value) { var node = document.getElementById(id); if (node) node.textContent = value; }
  function active(state) { return ['translating', 'staged', 'activation_prepared', 'checking_serving'].indexOf(state) >= 0; }
  function applyFilter() {
    if (!rows) return;
    var query = (search && search.value || '').toLocaleLowerCase('pl'), mode = filter && filter.value || 'all', count = 0;
    Array.prototype.forEach.call(rows.children, function (row) {
      var state = row.dataset.lunaState, show = row.textContent.toLocaleLowerCase('pl').indexOf(query) >= 0 && (mode === 'all' || mode === 'active' && active(state) || mode === 'missing' && row.dataset.lunaMissing === '1' || mode === state);
      row.hidden = !show; if (show) count++;
    });
    text('zpLunaRowsCount', count + ' widocznych / ' + rows.children.length + ' pozycji');
    var empty = document.getElementById('zpLunaEmpty'); if (empty) empty.hidden = count !== 0;
  }
  function element(name, value) { var node = document.createElement(name); if (typeof value === 'string') node.textContent = value; return node; }
  function link(value, label) {
    try { var u = new URL(value, location.href); if (u.origin !== location.origin || u.protocol !== location.protocol || u.username || u.password) return null; var a = element('a', label); a.href = u.href; a.target = '_blank'; a.rel = 'noopener'; return a; } catch (_) { return null; }
  }
  function updateRows(data) {
    if (!rows || !Array.isArray(data) || data.length > 1000) return;
    var fragment = document.createDocumentFragment();
    data.forEach(function (r) {
      if (!r || typeof r.source !== 'string' || typeof r.state !== 'string' || typeof r.label !== 'string') return;
      var row = element('tr'), name = element('td'), status = element('td'), map = element('td'), preview = element('td'), badge = element('span', r.label);
      row.dataset.lunaState = r.state; row.dataset.lunaMissing = r.has_en === false ? '1' : '0';
      name.appendChild(element('strong', typeof r.title === 'string' ? r.title : r.source)); name.appendChild(element('small', r.source));
      badge.className = 'zpLunaUI__badge'; if (/^[a-z_]+$/.test(r.state)) badge.classList.add('is-' + r.state); status.appendChild(badge);
      if (r.error) { var small = element('small'); small.appendChild(element('code', r.error)); status.appendChild(small); }
      map.textContent = r.has_en === true ? 'Dostępna' : r.has_en === false ? 'Brak mapy' : '—';
      [[r.pl_url, 'PL'], [r.has_en === true && r.en_url, 'EN'], [r.preview_url, 'Podgląd wersji']].forEach(function (q) { if (!q[0]) return; var a = link(q[0], q[1]); if (a) preview.appendChild(a); });
      [name, status, map, preview].forEach(function (td) { row.appendChild(td); }); fragment.appendChild(row);
    });
    rows.replaceChildren(fragment); applyFilter();
  }
  function update(d) {
    if (!d || d.available !== true || number(d.completed) === null || number(d.total) === null || d.completed > d.total) { if (live) live.textContent = 'Odczyt stanu niedostępny. Zachowano ostatni poprawny widok; nie rozpoczęto nowej kampanii.'; return false; }
    var percent = d.total ? (100 * d.completed / d.total).toFixed(1).replace('.', ',') + ' %' : '—';
    text('zpLunaHeading', d.heading); text('zpLunaState', d.label); text('zpLunaProgressText', d.completed + ' / ' + d.total + ' stron'); text('zpLunaPercent', percent);
    var progress = document.getElementById('zpLunaProgress'); if (progress) { progress.max = Math.max(1, d.total); progress.value = d.completed; }
    root.querySelectorAll('[data-luna-count]').forEach(function (node) { var key = node.dataset.lunaCount, value = d.counts && typeof d.counts === 'object' ? (Object.prototype.hasOwnProperty.call(d.counts, key) ? d.counts[key] : 0) : null, n = number(value); node.textContent = n === null ? '—' : String(n); });
    text('zpLunaCost', money(d.settled)); text('zpLunaReserved', money(d.reserved)); text('zpLunaLimit', money(d.limit));
    text('zpLunaChunks', (number(d.completed_chunks) === null ? '—' : d.completed_chunks) + ' / ' + (number(d.prepared_chunks) === null ? '—' : d.prepared_chunks));
    var error = document.getElementById('zpLunaError'); if (error) { error.hidden = !d.error; var code = error.querySelector('code'); if (code) code.textContent = d.error || ''; }
    updateRows(d.rows); if (live) live.textContent = 'Odczytano zapisaną kolejkę. ' + d.label + '. Błędy stron pozostają widoczne na liście.'; return true;
  }
  async function request(action, nonce, method) {
    var q = new URLSearchParams({ action: action, _wpnonce: nonce }), controller = new AbortController(), timeout = setTimeout(function () { controller.abort(); }, 22000);
    try { var response = await fetch(url.href + (method === 'GET' ? '?' + q : ''), { method: method, credentials: 'same-origin', signal: controller.signal, headers: method === 'POST' ? { 'Content-Type': 'application/x-www-form-urlencoded' } : {}, body: method === 'POST' ? q : null }); return await response.json(); } finally { clearTimeout(timeout); }
  }
  function nextDelay(response) {
    var d = response && response.success === true ? response.data : null, n = number(d && d.tick_wait_ms);
    return d && d.global_blocked === false && ['queued', 'translating', 'activating', 'processing'].indexOf(d.state) >= 0 && n !== null && n <= 5000 ? Math.max(250, n) : 5000;
  }
  async function poll() {
    if (stopped || ++calls > 360) { if (live) live.textContent = 'Automatyczny odczyt widoku zakończony. Kolejka i WP-Cron pozostają niezależne; odśwież stronę, aby czytać dalej.'; return; }
    var delay = 5000;
    try {
      var response = await request(c.status_action, c.status_nonce, 'GET');
      if (!response.success || !update(response.data)) { setTimeout(poll, 15000); return; }
      if (response.data.global_blocked !== false || ['queued', 'translating', 'activating', 'processing'].indexOf(response.data.state) < 0) { stopped = true; return; }
      calls = 0; // Renew the reader only after a confirmed active queue; unavailable reads remain bounded.
      // Existing native worker handles the actor, lease and accepted response IDs. Never start/retry a campaign here.
      if (response.data.can_tick === true) delay = nextDelay(await request('zp_en_rewrite_tick', c.tick_nonce, 'POST'));
    } catch (_) { if (live) live.textContent = 'Odczyt chwilowo niedostępny. Ostatni poprawny widok zachowany; nie ponowiono nieznanego płatnego żądania.'; }
    if (!stopped) setTimeout(poll, delay);
  }
  if (search) search.addEventListener('input', applyFilter); if (filter) filter.addEventListener('change', applyFilter);
  window.addEventListener('pagehide', function () { stopped = true; }); applyFilter(); poll();
}());
