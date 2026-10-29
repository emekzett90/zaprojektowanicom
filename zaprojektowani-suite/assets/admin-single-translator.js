(function () {
    'use strict';
    if (typeof zpSingleTranslator !== 'object' || !zpSingleTranslator) return;
    var cfg = zpSingleTranslator;
    if (cfg.page !== 'zp-tlumacz-en' && cfg.page !== 'zpl-languages') return;
    var scope = document.querySelector(cfg.page === 'zp-tlumacz-en' ? '.zpte' : '.zpl-admin');
    if (!scope) return;
    scope.querySelectorAll('a[href]').forEach(function (link) {
        try {
            var url = new URL(link.href, window.location.href);
            if (url.origin === window.location.origin && url.pathname.endsWith('/wp-admin/admin.php') && url.searchParams.get('page') === 'zpl-skaner') {
                var parent = link.parentElement;
                link.remove();
                if (parent && parent.tagName === 'P' && !parent.textContent.trim() && !parent.children.length) parent.remove();
            }
        } catch (_) {}
    });
    if (cfg.page === 'zpl-languages') {
        scope.querySelectorAll('p.description').forEach(function (p) {
            if (p.textContent.indexOf('Polskie teksty w wersji angielskiej') === 0 && p.textContent.indexOf('Zaprojektowani Skaner EN') !== -1) p.remove();
        });
        return;
    }
    if (!scope.querySelector('[data-zp-single-translator-tools]') && Array.isArray(cfg.tools)) {
        var tools = document.createElement('details');
        tools.className = 'zpxCard';
        tools.setAttribute('data-zp-single-translator-tools', '1');
        var summary = document.createElement('summary');
        summary.textContent = 'Korekty ręczne i ustawienia języka';
        tools.appendChild(summary);
        var explanation = document.createElement('p');
        explanation.textContent = 'Tłumacz EN korzysta z OpenAI. Narzędzia poniżej zachowują ręczne poprawki, adresy i wybór języka; nie uruchamiają drugiego translatora.';
        tools.appendChild(explanation);
        cfg.tools.forEach(function (item) {
            if (!item || typeof item.title !== 'string' || typeof item.url !== 'string') return;
            try {
                var url = new URL(item.url, window.location.href);
                if (url.origin !== window.location.origin || !url.pathname.endsWith('/wp-admin/admin.php') || url.searchParams.get('page') !== 'zpl-languages') return;
                var a = document.createElement('a');
                a.className = 'button'; a.href = url.href; a.textContent = item.title;
                tools.appendChild(a); tools.appendChild(document.createTextNode(' '));
            } catch (_) {}
        });
        var tabs = scope.querySelector('.zpxTabs');
        if (tabs) tabs.after(tools); else scope.appendChild(tools);
    }
    var progress = scope.querySelector('#zpte-progress');
    if (progress && !progress.querySelector('[data-zp-queue-explanation]')) {
        var title = progress.querySelector('#zpte-progress-title');
        if (title) title.textContent = 'Postęp bieżącej kolejki';
        var note = document.createElement('p');
        note.className = 'description'; note.setAttribute('data-zp-queue-explanation', '1');
        note.textContent = 'Procent dotyczy bieżącego przebiegu. Liczba przetłumaczonych stron obejmuje całą historię. Żywy status kolejki może zmienić się po wyświetleniu górnych liczników; odśwież stronę, aby porównać ten sam moment.';
        progress.appendChild(note);
    }
}());
