(function () {
    'use strict';
    var box = document.getElementById('zpte-progress');
    if (!box || typeof zpteAdmin === 'undefined') return;
    var cfg = zpteAdmin, working = false, stopped = false, timer, failures = 0;
    var formatter = new Intl.NumberFormat('pl-PL');
    function set(name, value) {
        document.querySelectorAll('[data-zpte="' + name + '"]').forEach(function (node) { node.textContent = value; });
    }
    function render(s) {
        box.dataset.state = s.state;
        ['done', 'total', 'queued', 'errors'].forEach(function (key) { set(key, formatter.format(s[key] || 0)); });
        set('percent', s.percent + '%');
        var bar = box.querySelector('[role="progressbar"]');
        bar.setAttribute('aria-valuenow', s.percent);
        bar.firstElementChild.style.width = s.percent + '%';
        set('message', s.message);
        set('current', s.current ? s.current + (s.page_total ? ' · teksty: ' + s.page_done + ' / ' + s.page_total : '') : '');
        set('retry', s.retry_label && (s.state === 'retry' || s.state === 'limit') ? 'Następna próba: ' + s.retry_label : '');
        if (s.counts) { Object.keys(s.counts).forEach(function (key) { set(key, formatter.format(s.counts[key])); }); }
        if (s.usage) { set('chars', formatter.format(s.usage.chars)); set('requests', formatter.format(s.usage.requests)); }
        document.getElementById('zpte-poll-status').textContent = 'Stan z ' + new Date().toLocaleTimeString('pl-PL') + '. Zostaw ten ekran otwarty, a tłumaczenie pójdzie dalej.';
    }
    async function request(action) {
        var controller = new AbortController(), timeout = setTimeout(function () { controller.abort(); }, action === 'zpte_step' ? 40000 : 15000);
        try {
            var response = await fetch(cfg.url, {
                method: 'POST', credentials: 'same-origin', cache: 'no-store', signal: controller.signal,
                headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
                body: new URLSearchParams({action: action, nonce: cfg.nonce}).toString()
            });
            if (response.status === 401 || response.status === 403) {
                stopped = true;
                throw new Error('Sesja lub uprawnienia wygasły. Odśwież panel, aby wznowić podgląd.');
            }
            var result = await response.json();
            if (!response.ok || !result.success || !result.data.progress) throw new Error('Panel nie otrzymał aktualnego statusu. Ponawiam połączenie; zapisany postęp pozostaje.');
            if (result.data.nonce) cfg.nonce = result.data.nonce;
            return result.data.progress;
        } finally { clearTimeout(timeout); }
    }
    function report(error) {
        document.getElementById('zpte-poll-status').textContent = stopped ? error.message : 'Chwilowy brak odpowiedzi serwera. Ponawiam; ostatni pokazany postęp może być nieaktualny.';
    }
    async function step() {
        if (working || stopped) return;
        working = true;
        try { await request('zpte_step'); }
        catch (error) { report(error); }
        finally { working = false; }
        // Read a fresh snapshot so an older worker response cannot move the displayed counters backwards.
        if (!stopped) { clearTimeout(timer); timer = setTimeout(poll, 500); }
    }
    async function poll() {
        if (stopped) return;
        try {
            var s = await request('zpte_status');
            failures = 0;
            render(s);
            if (s.can_step && !working) step();
        } catch (error) { failures++; report(error); }
        if (!stopped) timer = setTimeout(poll, Math.min(15000, 3000 * Math.max(1, failures)));
    }
    poll();
}());
