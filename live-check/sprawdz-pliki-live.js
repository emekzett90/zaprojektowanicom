// ZP Suite: compare live plugin files with Codex's ZIP manifest (read-only).
// Run in Chrome DevTools console (or the browser tool) on any https://zaprojektowani.com/wp-admin/ page while logged in as admin.
// It only GETs plugin-editor.php pages (no saves) and hashes the file text shown in the editor.
(async () => {
  const LIST = 'https://raw.githubusercontent.com/emekzett90/zaprojektowanicom/rollback-codex-2026-10-13/live-check/pliki.json';
  const files = await (await fetch(LIST, { cache: 'no-store' })).json();
  const enc = new TextEncoder();
  const hex = (buf) => [...new Uint8Array(buf)].map((b) => b.toString(16).padStart(2, '0')).join('');
  const sha = async (s) => hex(await crypto.subtle.digest('SHA-256', enc.encode(s)));
  const out = { started: new Date().toISOString(), checked: 0, same: 0, differ: [], unreadable: [] };
  window.zpLiveDiffContent = {};
  let i = 0;
  const worker = async () => {
    while (i < files.length) {
      const [path, zipSha] = files[i++];
      const url = '/wp-admin/plugin-editor.php?plugin=' + encodeURIComponent('zaprojektowani-suite/zaprojektowani-suite.php') + '&file=' + encodeURIComponent(path);
      try {
        const html = await (await fetch(url, { credentials: 'same-origin', cache: 'no-store' })).text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const ta = doc.querySelector('textarea#newcontent');
        if (!ta) { out.unreadable.push(path); continue; }
        const text = ta.textContent;
        const variants = [text, '\n' + text, text.replace(/\n/g, '\r\n'), '\n' + text.replace(/\n/g, '\r\n')];
        let match = false;
        for (const v of variants) { if ((await sha(v)) === zipSha) { match = true; break; } }
        out.checked++;
        if (match) { out.same++; } else { out.differ.push({ path, liveSha: await sha(text), zipSha, liveBytes: enc.encode(text).length }); window.zpLiveDiffContent[path] = text; }
      } catch (e) { out.unreadable.push(path + ' (' + e.message + ')'); }
    }
  };
  await Promise.all([worker(), worker(), worker()]);
  out.finished = new Date().toISOString();
  window.zpLiveCheck = out;
  console.log(JSON.stringify(out, null, 1));
  return out;
})();
