"""
Check the guides in build/guides/*.json before a build (docs/editorial-standards.md).

Fails on: a text pair missing English or Spanish, an em dash, a state row without a source, a checked
date or a quoted rule, an unknown category or who-can-vote value, a review-due date that has passed,
dates out of order, or a title too long for search results.

    python3 -I build/guide_check.py            # exit 1 on any problem
"""
import json
import re
import sys
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
TODAY = date.today().isoformat()
ISO = re.compile(r'^\d{4}-\d{2}-\d{2}$')
errors = []


def pairs(node, path):
    """Every dict with an 'en' key is a text pair and needs both languages."""
    if isinstance(node, dict):
        if 'en' in node and isinstance(node['en'], str):
            for lang in ('en', 'es'):
                if lang not in node or not isinstance(node[lang], str):
                    errors.append(f'{path}: missing {lang}')
            if node.get('en', '') == '' and node.get('es', '') != '':
                errors.append(f'{path}: Spanish without English')
        for k, v in node.items():
            pairs(v, f'{path}.{k}')
    elif isinstance(node, list):
        for i, v in enumerate(node):
            pairs(v, f'{path}[{i}]')


def check(f):
    g = json.loads(f.read_text())
    name = f.stem
    text = f.read_text()
    if '—' in text:
        errors.append(f'{name}: em dash')
    pairs(g, name)
    if g.get('review_due', '') < TODAY:
        errors.append(f'{name}: review was due {g.get("review_due")}; review the facts and set a new date')
    for lang, d in g['dates'].items():
        for k in ('published', 'reviewed'):
            if not ISO.match(d.get(k) or ''):
                errors.append(f'{name}: {lang} {k} date missing')
        if d.get('updated') and d['updated'] <= d['published']:
            errors.append(f'{name}: {lang} updated is not after published')
        if (d.get('published') or '') > TODAY:
            errors.append(f'{name}: {lang} published date is in the future')
    for lang in ('en', 'es'):
        t = g['meta']['title'][lang]
        if len(t.removesuffix(' | Vote Sticker')) > 65 or not t.endswith(' | Vote Sticker'):
            errors.append(f'{name}: {lang} title {t!r}')
    seen = set()
    for s in g.get('states', []):
        a = s.get('abbr', '?')
        if a in seen:
            errors.append(f'{name}: {a} listed twice')
        seen.add(a)
        if s.get('cat') not in g['categories']:
            errors.append(f'{name}: {a} category {s.get("cat")}')
        if not ISO.match(s.get('checked', '')) or s['checked'] > TODAY:
            errors.append(f'{name}: {a} checked date')
        if not s.get('sources') or any(not x.get('url', '').startswith('https://') or not x.get('title') for x in s['sources']):
            errors.append(f'{name}: {a} needs at least one https source with a title')
        th = s.get('threshold', {})
        if not th.get('quote') or not th.get('cite') or th.get('op') not in ('>', '>=') or not 0 < th.get('value', 0) < 100:
            errors.append(f'{name}: {a} threshold needs value, op, quote and cite')
        tm = s.get('timing', {})
        if not 0 < tm.get('min', 0) <= tm.get('max', 0) <= 13:
            errors.append(f'{name}: {a} timing weeks')
        for k in ('cross', 'skip', 'register'):
            if s.get('who', {}).get(k, {}).get('v') not in ('yes', 'no', 'dep', 'unk'):
                errors.append(f'{name}: {a} who.{k}')
    return g


def main():
    files = sorted((ROOT / 'build' / 'guides').glob('*.json'))
    for f in files:
        check(f)
    if errors:
        print('\n'.join(errors))
        sys.exit(1)
    print(f'guides ok: {len(files)}')


if __name__ == '__main__':
    main()
