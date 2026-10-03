#!/usr/bin/env python3
"""Writes a .min.css next to every plugin stylesheet over 4 KB (rcssmin: comments and whitespace only).

Run from the repository root after editing CSS:  python3 tools/minify_css.py
assets/css-min.json records each source's size; the plugin (includes/css-min.php) serves the
.min.css only while the source still has that size, so an edited source is served as is.
"""
import json
import pathlib
import rcssmin

root = pathlib.Path(__file__).resolve().parent.parent / 'zaprojektowani-suite' / 'assets'
before = after = 0
manifest = {}
for src in sorted(root.rglob('*.css')):
    if src.name.endswith('.min.css') or src.stat().st_size < 4096:
        continue
    text = src.read_text(encoding='utf-8')
    out = rcssmin.cssmin(text, keep_bang_comments=True)
    dst = src.with_name(src.name[:-4] + '.min.css')
    dst.write_text(out, encoding='utf-8')
    manifest['assets/' + src.relative_to(root).as_posix()] = src.stat().st_size
    before += len(text.encode())
    after += len(out.encode())
(root / 'css-min.json').write_text(json.dumps(manifest, indent=1, sort_keys=True) + '\n', encoding='utf-8')
print(f'{before // 1024} KB -> {after // 1024} KB')
