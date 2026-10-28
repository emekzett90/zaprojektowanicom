/* Only the documented Rank Math analysis filter. Never changes an editor or Save payload. */
(function () {
  'use strict';
  const c = window.zpRankMathContentBridge;
  if (!c || !window.wp || !wp.hooks || !Number.isSafeInteger(c.postId) || c.postId < 1) return;
  let projection = null;
  function normalize(value) {
    return String(value).replace(/<\/?p\s*>|<br\s*\/?>/gi, '').trim().replace(/\s+/g, ' ');
  }
  const baseline = normalize(c.savedContent);
  const emptyRoute = c.allowEmptyRoute === true && c.postId === 4266 && c.savedContent === ''; 
  const original = document.getElementById('content');
  const mode = c.editorMode;
  if (mode !== 'classic' && mode !== 'block') return;
  function blockSnapshot() {
    try {
      if (!wp.data || typeof wp.data.select !== 'function') return null;
      const editor = wp.data.select('core/editor');
      if (!editor || ['getCurrentPostId', 'getCurrentPostType', 'getEditedPostContent', 'getEditedPostAttribute', 'hasChangedContent']
        .some(function (name) { return typeof editor[name] !== 'function'; })) return null;
      const result = {id: editor.getCurrentPostId(), type: editor.getCurrentPostType(), content: editor.getEditedPostContent(),
        title: editor.getEditedPostAttribute('title'), status: editor.getEditedPostAttribute('status'),
        password: editor.getEditedPostAttribute('password'), changed: editor.hasChangedContent()};
      return result.id == null || typeof result.content !== 'string' || (!result.content && !emptyRoute)
        || typeof result.title !== 'string' || !result.title || typeof result.status !== 'string'
        || typeof result.password !== 'string' || typeof result.changed !== 'boolean' ? null : result;
    } catch (_) { return null; }
  }
  function unchanged() {
    if (mode === 'block') {
      const state = blockSnapshot();
      return Boolean(state && state.id === c.postId && state.type === 'page' && state.status === 'publish' && state.password === ''
        && state.changed === false && normalize(state.content) === baseline && state.title === c.savedTitle);
    }
    if (!original || normalize(original.value) !== baseline) return false;
    const title = document.getElementById('title');
    if (title && title.value !== c.savedTitle) return false;
    const editor = window.tinymce && tinymce.get('content');
    return !editor || editor.isHidden() || normalize(editor.getContent({format: 'raw'})) === baseline;
  }
  function input(content) {
    return projection && unchanged() && normalize(content) === baseline ? projection.html : content;
  }
  wp.hooks.addFilter('rank_math_content', 'zaprojektowani/rendered-main-content', input, 30);
  const status = document.createElement('p');
  status.className = 'description';
  status.setAttribute('role', 'status');
  status.textContent = 'Rank Math: sprawdzam rzeczywistą treść publicznej strony.';
  if (original && original.parentNode) original.parentNode.appendChild(status);
  else {
    const outer = document.getElementById('wpbody-content');
    if (outer) outer.appendChild(status);
  }
  let endpoint;
  try { endpoint = new URL(c.ajax, location.href); } catch (_) { return; }
  if (endpoint.origin !== location.origin || !/\/wp-admin\/admin-ajax\.php$/.test(endpoint.pathname)) return;
  let started = false;
  let unsubscribe = null;
  let waitTimer = null;
  function stopWaiting() {
    if (unsubscribe) { unsubscribe(); unsubscribe = null; }
    if (waitTimer !== null) { clearTimeout(waitTimer); waitTimer = null; }
  }
  function start() {
    if (started || (mode === 'block' && !blockSnapshot())) return;
    started = true;
    stopWaiting();
    if (!unchanged()) {
      status.textContent = 'Treść, tytuł lub widoczność edytora różni się od zapisanej strony. Rank Math zachowuje własną analizę.';
      return;
    }
    const body = new URLSearchParams({action: c.action, post_id: String(c.postId), nonce: c.nonce, source_hash: c.sourceHash});
    fetch(endpoint.href, {method: 'POST', credentials: 'same-origin', body: body, headers: {'Accept': 'application/json'}})
    .then(function (response) { if (!response.ok) throw new Error('unavailable'); return response.json(); })
    .then(function (result) {
      const d = result && result.success === true && result.data;
      if (!d || d.postId !== c.postId || d.sourceHash !== c.sourceHash || d.version !== 'editor-main-v4-block-boundaries'
        || typeof d.html !== 'string' || !d.html || d.html.length > 350000 || !/^[a-f0-9]{64}$/.test(d.projectionHash)
        || !Number.isSafeInteger(d.wordCount) || d.wordCount < 1 || d.wordCount > 100000) throw new Error('unavailable');
      if (!unchanged()) throw new Error('changed');
      projection = d;
      status.textContent = 'Zweryfikowano treść główną publicznej strony (' + d.wordCount + ' słów). Adapter używa jej wyłącznie zamiast niezmienionej treści edytora; zapis edytora pozostaje bez zmian. Wynik oblicza Rank Math.';
      if (window.rankMathEditor && typeof rankMathEditor.refresh === 'function') rankMathEditor.refresh('content');
      else status.textContent += ' Panel analizatora nie jest teraz dostępny.';
    }).catch(function () {
      projection = null;
      status.textContent = 'Nie potwierdzono aktualnej treści publicznej strony. Rank Math używa własnej treści edytora; po zapisaniu lub zmianie strony odśwież edytor.';
    });
  }
  function boot() {
    start();
    if (!started && mode === 'block' && wp.data && typeof wp.data.subscribe === 'function') {
      unsubscribe = wp.data.subscribe(start);
      waitTimer = setTimeout(function () {
        started = true;
        stopWaiting();
        status.textContent = 'Edytor nie udostępnił aktualnej treści. Rank Math zachowuje własną analizę.';
      }, 20000);
      start();
    }
  }
  if (typeof wp.domReady === 'function') wp.domReady(boot);
  else boot();
}());
