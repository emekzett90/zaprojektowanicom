/* Tłumacz EN: live progress. While work is queued and the background steps have stopped, this open panel runs them.
   2.1.0: the panel shows one language (EN, DE or ES tab) and asks about that one. */
(function () {
    'use strict';
    var cfg = window.zpte2Admin;
    var box = document.getElementById('zpte2-progress');
    if (!cfg || !box || !window.fetch || !window.URLSearchParams) { return; }
    var state = null;
    var failures = 0;
    var timer = 0;
    var busy = false;

    function set(name, value) {
        var els = box.querySelectorAll('[data-zpte2="' + name + '"]');
        for (var i = 0; i < els.length; i++) { els[i].textContent = value; }
    }

    function render(p) {
        var pct = Math.max(0, Math.min(100, parseInt(p.percent, 10) || 0));
        set('percent', pct + '%');
        var bar = box.querySelector('.zpte2-bar');
        if (bar) {
            bar.setAttribute('aria-valuenow', String(pct));
            var fill = bar.querySelector('span');
            if (fill) { fill.style.width = pct + '%'; }
        }
        set('message', p.message || (p.work ? 'Tłumaczę…' : 'Czekam na następne sprawdzenie.'));
        set('done', String(p.done || 0));
        set('total', String(p.total || 0));
        set('queued', String(p.queued || 0));
        set('en', String(p.en || 0));
        set('attention', String(p.attention || 0));
        set('current', p.work && p.current ? 'Teraz: ' + p.current : '');
        box.setAttribute('data-work', p.work ? '1' : '0');
    }

    function schedule(ms) {
        window.clearTimeout(timer);
        timer = window.setTimeout(tick, ms);
    }

    function tick() {
        if (busy) { return; }
        if (document.hidden) { schedule(15000); return; }
        busy = true;
        var action = state && state.work && state.stalled ? 'zpte2_step' : 'zpte2_status';
        var body = new URLSearchParams();
        body.append('action', action);
        body.append('nonce', cfg.nonce);
        body.append('zpte_lang', cfg.lang || 'en');
        fetch(cfg.url, { method: 'POST', credentials: 'same-origin', body: body })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (res) {
                busy = false;
                if (!res || !res.success || !res.data || !res.data.progress) {
                    // Session ended or the page is stale: stop asking until the panel is reloaded.
                    if (res && res.data && res.data.reload) { return; }
                    throw new Error('status');
                }
                if (res.data.nonce) { cfg.nonce = res.data.nonce; }
                state = res.data.progress;
                failures = 0;
                render(state);
                schedule(state.work ? (state.stalled ? 1000 : 4000) : 30000);
            })
            .catch(function () {
                busy = false;
                failures++;
                schedule(Math.min(60000, 5000 * failures));
            });
    }

    document.addEventListener('visibilitychange', function () { if (!document.hidden) { schedule(300); } });
    schedule(box.getAttribute('data-work') === '1' ? 1500 : 30000);
}());
