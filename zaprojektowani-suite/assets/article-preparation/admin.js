(function () {
  'use strict';
  const config = window.zpContentPreparation;
  const region = document.getElementById('zp-prep-live');
  if (!config || !region) return;
  let endpoint;
  try { endpoint = new URL(config.ajax, window.location.href); } catch (_) { return; }
  if (endpoint.origin !== window.location.origin) return;
  endpoint.searchParams.set('action', 'zp_prep_status');
  endpoint.searchParams.set('_wpnonce', config.nonce);
  const labels = { queued: 'Oczekuje', running: 'Praca w toku', ready: 'Szkic gotowy', needs_retry: 'Wymaga sprawdzenia', scheduled: 'Zaplanowany', complete: 'Tematy dobrane', schedule_attention: 'Sprawdź termin', publish_attention: 'Sprawdź wynik publikacji', publishing: 'Sprawdzamy publikację', policy_hold: 'Wstrzymano: wolumen poniżej 150' };
  let polls = 0;
  async function poll() {
    if (++polls > 180) return;
    try {
      const response = await fetch(endpoint.href, { credentials: 'same-origin', cache: 'no-store' });
      if (!response.ok) return;
      const result = await response.json();
      if (!result.success || !Array.isArray(result.data && result.data.jobs)) return;
      const jobs = result.data.jobs;
      const auto = result.data.auto;
      region.replaceChildren();
      const title = document.createElement('p');
      const nativeStates = ['draft', 'future', 'publish', 'pending', 'private', 'trash', 'auto-draft'];
      const missingStatus = jobs.some(j => Number.isInteger(j.post_id) && j.post_id > 0 && j.native_status !== false && !nativeStates.includes(j.native_status));
      const ready = missingStatus ? '—' : jobs.filter(j => j.state === 'ready' && j.native_status === 'draft').length;
      const future = missingStatus ? '—' : jobs.filter(j => j.native_status === 'future').length;
      const published = missingStatus ? '—' : jobs.filter(j => j.native_status === 'publish').length;
      const unavailable = jobs.filter(j => Number.isInteger(j.post_id) && j.post_id > 0 && j.native_status === false).length;
      title.textContent = 'Bieżący stan · Gotowe szkice: ' + ready + ' · Zaplanowane: ' + future + ' · Opublikowane: ' + published + (unavailable ? ' · Wpisy niedostępne: ' + unavailable : '') + (missingStatus ? ' · Brak pełnych danych o statusie WordPressa.' : '');
      region.append(title);
      if (auto && typeof auto.state === 'string') { const progress = document.createElement('p'); progress.textContent = 'Automatyczny dobór: ' + (labels[auto.state] || 'Sprawdź stan') + ', zbadano ' + auto.seeds_completed + '/' + auto.seeds_total + ' obszarów, ' + auto.candidates + ' kandydatów z wolumenem.'; region.append(progress); }
      const list = document.createElement('ul');
      jobs.slice(-10).forEach(job => {
        const item = document.createElement('li');
        const nativeLabel = job.native_status === 'publish' ? 'Opublikowany w WordPressie' : (job.native_status === 'future' ? 'Zaplanowany w WordPressie' : (Number.isInteger(job.post_id) && job.post_id > 0 && job.native_status === false ? 'Wpis niedostępny w WordPressie' : (Number.isInteger(job.post_id) && job.post_id > 0 && !nativeStates.includes(job.native_status) ? 'Brak danych o statusie WordPressa' : '')));
        const queueLabel = typeof job.status_label === 'string' && job.status_label ? job.status_label : (labels[job.state] || 'Sprawdź stan');
        const attention = ['needs_retry', 'publish_attention', 'schedule_attention', 'policy_hold', 'publishing'].includes(job.state);
        item.textContent = job.keyword + ' — ' + (nativeLabel || queueLabel) + (nativeLabel && attention ? ' · Stan kolejki: ' + (labels[job.state] || 'Sprawdź stan kolejki') : '') + ' ';
        if (job.response_pending && Number.isFinite(job.response_elapsed_sec)) item.append(document.createTextNode('(' + Math.floor(job.response_elapsed_sec / 60) + ' min) '));
        if (typeof job.preview === 'string') {
          try {
            const url = new URL(job.preview, window.location.href);
            if (url.origin === window.location.origin && /^https?:$/.test(url.protocol)) {
              const link = document.createElement('a'); link.href = url.href; link.textContent = 'Podgląd WordPress'; link.target = '_blank'; link.rel = 'noopener'; item.append(link);
            }
          } catch (_) {}
        }
        list.append(item);
      });
      region.append(list);
      const refresh = document.createElement('button'); refresh.type = 'button'; refresh.className = 'button'; refresh.textContent = 'Odśwież tabelę i gotowe operacje'; refresh.addEventListener('click', () => window.location.reload()); region.append(refresh);
      if (jobs.some(j => j.state === 'queued' || j.state === 'running') || (auto && (auto.state === 'queued' || auto.state === 'running'))) window.setTimeout(poll, 15000);
    } catch (_) {
      region.textContent = 'Nie udało się pobrać aktualnego stanu. Możesz odświeżyć stronę; to nie ponawia generacji.';
    }
  }
  poll();
})();
