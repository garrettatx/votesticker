"""Render docs/round3-prompts-one-state.md as a copy-ready HTML page (one Copy button per prompt).
    python3 -I build/prompts_html.py"""
import html
import re
from pathlib import Path

DOCS = Path(__file__).resolve().parent.parent / 'docs'
s = (DOCS / 'round3-prompts-one-state.md').read_text()
parts = re.split(r'```\n(.*?)```', s, flags=re.S)
out, n = [], 0
for i, p in enumerate(parts):
    if i % 2:
        n += 1
        out.append(f'<div class="blk"><button onclick="navigator.clipboard.writeText(document.getElementById(\'b{n}\').innerText);this.textContent=\'Copied\'">Copy</button><pre id="b{n}">{html.escape(p.rstrip())}</pre></div>')
        continue
    for line in p.splitlines():
        if line.startswith('<!--') or not line.strip():
            continue
        m = re.match(r'(#+) (.*)', line)
        done = re.match(r'- ~~(.+?)~~ (.*)', line)
        if done:
            out.append(f'<p class="done"><input type="checkbox" checked disabled aria-label="{html.escape(done.group(1))} done"> <s>{html.escape(done.group(1))}</s> {html.escape(done.group(2))}</p>')
        elif m and m.group(1) == '####':
            key = re.sub(r'[^a-z]+', '-', m.group(2).lower())
            out.append(f'<h4><label><input type="checkbox" class="track" data-key="{key}"> {html.escape(m.group(2))}</label></h4>')
        elif m:
            lvl = min(len(m.group(1)), 4)
            out.append(f'<h{lvl}>{html.escape(m.group(2))}</h{lvl}>')
        else:
            out.append('<p>' + re.sub(r'`([^`]+)`', r'<code>\1</code>', html.escape(line)) + '</p>')
# Ticking a state's box strikes it through; the ticks are remembered in this browser only.
SCRIPT = """<script>document.querySelectorAll('.track').forEach(function(c){var k='vs-prompt:'+c.dataset.key;try{c.checked=localStorage.getItem(k)==='1'}catch(e){}var h=c.closest('h4');var set=function(){h.classList.toggle('is-done',c.checked)};set();c.addEventListener('change',function(){try{localStorage.setItem(k,c.checked?'1':'0')}catch(e){}set()})});</script>"""
css = """:root{--bg:#FBF8F0;--ink:#14295E;--text:#1F2430;--line:#E3DED2;--panel:#fff}
@media (prefers-color-scheme: dark){:root:not([data-theme="light"]){--bg:#12161f;--ink:#c9d6f5;--text:#e6e8ee;--line:#2c3342;--panel:#1a2030}}
:root[data-theme="dark"]{--bg:#12161f;--ink:#c9d6f5;--text:#e6e8ee;--line:#2c3342;--panel:#1a2030}
body{font:16px/1.5 system-ui,sans-serif;max-width:860px;margin:0 auto;padding:24px 16px;background:var(--bg);color:var(--text)}
h1,h2,h3,h4{color:var(--ink)}h4{margin:28px 0 8px}
pre{white-space:pre-wrap;background:var(--panel);border:1px solid var(--line);padding:40px 14px 14px;border-radius:8px;font-size:14px}
.done{margin:4px 0;color:#6b7080}h4 label{cursor:pointer;display:flex;gap:10px;align-items:center}h4 input,.done input{width:20px;height:20px}h4.is-done{opacity:.55;text-decoration:line-through}.blk{position:relative}button{position:absolute;right:8px;top:8px;background:#14295E;color:#fff;border:0;border-radius:6px;padding:6px 12px;cursor:pointer}"""
(DOCS / 'round3-prompts-one-state.html').write_text(f'<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Vote Sticker Image Prompts</title><style>{css}</style></head><body>\n' + '\n'.join(out) + SCRIPT + '\n</body></html>\n')
print(n, 'copy blocks')
