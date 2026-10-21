"""
QA for the state voting guide (build/state-facts/ -> site/facts-states.php -> the state pages).

Checks: every facts file passes build/facts_text.py --check; site/facts-states.php is current; each
state with facts renders its guide sections in both languages; the FAQPage JSON-LD asks exactly the
questions shown on the page with the same answers; every guide fact links an official source; no em
dashes in the guide. States without a facts file must render no guide.

    python3 -I scripts/qa_facts.py [--base http://127.0.0.1:8791/]
"""
import html
import json
import re
import subprocess
import sys
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
args = sys.argv[1:]
BASE = args[args.index('--base') + 1] if '--base' in args else 'http://127.0.0.1:8791/'
fails = []

r = subprocess.run([sys.executable, '-I', str(ROOT / 'build' / 'facts_text.py'), '--check'], capture_output=True, text=True)
print(r.stdout.strip())
if r.returncode:
    fails.append('facts_text.py --check failed')

facts = {p.stem.upper() for p in (ROOT / 'build' / 'state-facts').glob('*.json')}
php = (ROOT / 'site' / 'facts-states.php').read_text() if (ROOT / 'site' / 'facts-states.php').exists() else ''
for a in sorted(facts):
    if f"'{a}' =>" not in php:
        fails.append(f'{a}: site/facts-states.php is out of date; run python3 -I build/facts_text.py')
    for p in sorted((ROOT / 'build' / 'state-facts').glob('*.json')):
        if p.stat().st_mtime > (ROOT / 'site' / 'facts-states.php').stat().st_mtime:
            fails.append(f'{p.name} is newer than site/facts-states.php; run python3 -I build/facts_text.py')
            break

slugs = {}
for m in re.finditer(r'"abbr": "([A-Z]{2})", "name": "([^"]+)"', (ROOT / 'site' / 'states.js').read_text()):
    slugs[m.group(1)] = re.sub(r'[^a-z]+', '-', m.group(2).lower()).strip('-')


def page(path):
    with urllib.request.urlopen(urllib.request.Request(BASE + path, headers={'User-Agent': 'votesticker-qa'})) as f:
        return f.read().decode()


checked = 0
for abbr, slug in slugs.items():
    for lang in ('', 'es/'):
        h = page(lang + slug + '/')
        guide = re.findall(r'<section class="guide".*?</section>', h, re.S)
        if abbr not in facts:
            if guide:
                fails.append(f'{lang}{slug}: renders a guide without a facts file')
            continue
        checked += 1
        if not guide:
            fails.append(f'{lang}{slug}: facts file but no guide sections')
        g = ''.join(guide)
        if '—' in g:
            fails.append(f'{lang}{slug}: em dash in the guide')
        for sec in guide:
            if sec.count('class="cite"') != 1:
                fails.append(f'{lang}{slug}: a guide section has {sec.count(chr(99)+"lass=")} source lines, not one: {re.sub("<[^>]+>", " ", sec)[:50]}')
    # FAQ schema must match the visible state questions, both languages.
    if abbr in facts:
        for lang in ('', 'es/'):
            h = page(lang + slug + '/')
            vis = [(html.unescape(q), html.unescape(a)) for q, a in re.findall(r'<details class="faq__state"><summary>(.*?)</summary><p>(.*?)</p>', h)]
            ld = next((json.loads(x) for x in re.findall(r'<script type="application/ld\+json">(.*?)</script>', h, re.S) if '"FAQPage"' in x), None)
            got = [(q['name'], q['acceptedAnswer']['text']) for q in (ld or {}).get('mainEntity', [])]
            if vis != got:
                fails.append(f'{lang}{slug}: FAQPage JSON-LD does not match the visible questions ({len(got)} vs {len(vis)})')

print(f'{len(facts)} state(s) with facts, {checked} page(s) checked')
for f in fails:
    print('FAIL ' + f)
print('FAILED' if fails else 'PASS')
sys.exit(1 if fails else 0)
