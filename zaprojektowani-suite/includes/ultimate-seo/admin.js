(() => {
  'use strict';
  const status = document.getElementById('zpar-progress');
  const bar = document.getElementById('zpar-bar');
  if (!status || !bar) return;
  async function refresh() {
    try {
      const body = new URLSearchParams({ action: 'zpsseo_status', _ajax_nonce: ZPAR.nonce });
      const response = await fetch(ZPAR.url, { method: 'POST', credentials: 'same-origin', body });
      const payload = await response.json();
      if (!response.ok || !payload.success) throw new Error('Podgląd chwilowo niedostępny.');
      const data = payload.data;
      data.sources.forEach((source, i) => {
        const cell = document.getElementById(`zpar-result-${i}`);
        if (cell && data.results[source]) cell.textContent = data.results[source];
      });
      bar.value = Math.max(0, ZPAR.total - data.pending);
      status.textContent = data.done ? 'Automatyczny przebieg zakończony. Poniżej wyniki i ewentualne ograniczenia.' : `Naprawa działa w tle. Pozostało ${data.pending} operacji. Możesz zamknąć tę kartę.`;
      if (data.done) return;
    } catch (error) { status.textContent = `${error.message} Naprawy serwerowe są niezależne od tego podglądu.`; }
    window.setTimeout(refresh, 10000);
  }
  refresh();
})();
