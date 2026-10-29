(() => {
  'use strict';
  const node = document.getElementById('zp-prep-photo-live');
  const config = window.zpPreparedPhotos;
  if (!node || !config || !config.url || !config.nonce) return;
  const started = Date.now();
  let failures = 0;
  const labels = {queued:'oczekuje',running:'powstaje zdjęcie',applying:'zapis zdjęć',applied:'zdjęcia wymienione',needs_retry:'wymaga ręcznego sprawdzenia',cancelled:'wstrzymano',rolled_back:'przywrócono poprzednie zdjęcia'};
  const poll = async () => {
    if (Date.now() - started > 7200000) {node.textContent = 'Odśwież stronę, aby sprawdzić ostatni zapis poprawy zdjęć.'; return;}
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 10000);
    let active = true;
    try {
      const url = new URL(config.url, window.location.href);
      if (url.origin !== window.location.origin) return;
      url.searchParams.set('action', 'zp_prep_photos_status');
      url.searchParams.set('nonce', config.nonce);
      const response = await fetch(url, {credentials:'same-origin',cache:'no-store',signal:controller.signal});
      const payload = await response.json();
      if (!response.ok || !payload.success || !Array.isArray(payload.data)) throw new Error('status');
      failures = 0;
      node.textContent = payload.data.map(job => `Szkic #${Number(job.post_id) || 0}: ${labels[job.state] || 'sprawdź zapis'}`).join(' · ');
      active = payload.data.some(job => ['queued','running','applying'].includes(job.state));
      if (!active) node.textContent += ' Obejrzyj podgląd lub odśwież tabelę przed dalszą decyzją.';
    } catch (_) {
      failures += 1;
      node.textContent = 'Nie udało się odczytać ostatniego stanu zdjęć. Generacji nie ponawiamy automatycznie; odśwież stronę.';
    } finally {
      clearTimeout(timeout);
      if (active && failures < 5) setTimeout(poll, document.hidden ? 60000 : 15000);
    }
  };
  poll();
})();
