"""Descriptive image filenames (loop item 11), October 26, 2026. Map only: nothing is renamed yet.

`tx-vote-1.jpg` says nothing to image search. This writes build/image-names.json, one entry per
ready-made sticker: the old path, a new name built from its alt text (state, the words it shows,
color, outline), and the 301 line for Cloudflare Pages `_redirects`. The switch waits until after
Election Day (backlog: "only with redirects from the old URLs; after the election").

  python3 -I build/image_names.py           # write the map
  python3 -I build/image_names.py --check   # validate only (exit 1 on a problem)

Before the switch, these read the old names and must read the map instead:
- site/index.php: vote_files() lists files in natural order (the gallery order comes from the
  number in the name), vote_share_image() builds "$k-vote-$n.jpg", the hero and showcase pick
  "<abbr>-vote-<n>.jpg", alts.json is keyed by the old stem.
- site/history.php: tx-vote-2.jpg twice. build/sticker_collection.py uses the stem as the record id.
- scripts/render_readymades.mjs writes the old names; scripts/qa_cards.mjs and the image sitemap
  follow whatever is on disk.
"""
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
STATES = ROOT / 'site' / 'assets' / 'states'
OUT = ROOT / 'build' / 'image-names.json'
MONTHS = r'jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec'


def slug(s):
    return re.sub(r'[^a-z0-9]+', '-', s.lower()).strip('-')


def words(says, state, abbr):
    """The sticker's message without dates, years or the state's name or postal code: names outlive the election."""
    says = re.sub(r'\b%s\b' % abbr, ' ', says, flags=re.I) if abbr not in ('IN', 'OR', 'ME', 'OK', 'HI') else re.sub(r'\b%s\b' % abbr, ' ', says)
    says = re.sub(r'\b(%s)\b\.?' % MONTHS, ' ', says, flags=re.I)
    says = re.sub(r'\d+', ' ', says)
    says = re.sub(r'\b(by|through|due|until)\b\s*(?=[,–\-]|$)', ' ', says, flags=re.I)
    says = re.sub(r'\b%s\b' % re.escape(state), ' ', says, flags=re.I)
    return slug(says)


def name_for(alt, state, abbr):
    m = re.match(r'(?:Sticker in (?P<colors>.+?) colors|(?P<color>.+?) sticker)(?P<outline> with an? .+? outline)?(?:.*?)reading (?P<says>.+)$', alt)
    if not m:
        return None
    color = 'state-colors' if m['colors'] else slug(m['color'])
    parts = [slug(state), words(m['says'], state, abbr) or 'vote', 'sticker', color]
    if m['outline']:
        parts.append('outline')
    return '-'.join(p for p in parts if p)


def build():
    alts = json.loads((STATES / 'alts.json').read_text())
    names = {m[0].lower(): m[1] for m in re.findall(r'"abbr": "(\w\w)", "name": "([^"]+)"', (ROOT / 'site' / 'states.js').read_text())}
    rows, bad, seen = [], [], {}
    for d in sorted(p for p in STATES.iterdir() if p.is_dir()):
        state = names.get(d.name)
        if not state:
            bad.append(f'{d.name}: no state name in site/states.js')
            continue
        for f in sorted(d.glob('*.jpg'), key=lambda p: [int(t) if t.isdigit() else t for t in re.split(r'(\d+)', p.stem)]):
            if f.stem.startswith(slug(state) + '-'):
                continue  # already named for people (scripts/render_bespoke.mjs, October 26)
            new = name_for(alts.get(f.stem, ''), state, d.name.upper())
            if not new:
                bad.append(f'{f.stem}: alt text does not parse ({alts.get(f.stem)!r})')
                continue
            n = seen[new] = seen.get(new, 0) + 1
            if n > 1:
                new = f'{new}-{n}'
            old = f'/assets/states/{d.name}/{f.name}'
            rows.append({'old': old, 'new': f'/assets/states/{d.name}/{new}.jpg', 'order': len([r for r in rows if r['old'].split('/')[3] == d.name]) + 1,
                         'redirect': f'{old} /assets/states/{d.name}/{new}.jpg 301'})
    return rows, bad


def check(rows):
    bad = []
    news = [r['new'] for r in rows]
    bad += [f'duplicate new name {n}' for n in sorted({n for n in news if news.count(n) > 1})]
    for r in rows:
        stem = r['new'].rsplit('/', 1)[1][:-4]
        if not re.fullmatch(r'[a-z0-9]+(-[a-z0-9]+)*', stem):
            bad.append(f"{r['old']}: new name {stem!r} is not lowercase-hyphen")
        if len(stem) > 80:
            bad.append(f"{r['old']}: new name longer than 80 characters")
        if re.search(r'\d{4}|\b(%s)\b' % MONTHS, stem.replace('-', ' ')):
            bad.append(f"{r['old']}: new name carries a date ({stem})")
        if r['old'] == r['new']:
            bad.append(f"{r['old']}: unchanged")
    if len(rows) > 2000:
        bad.append(f'{len(rows)} redirects; Cloudflare Pages allows 2,000 static rules')
    return bad


def main():
    rows, bad = build()
    bad += check(rows)
    for b in bad:
        print('FAIL', b)
    print(f'{len(rows)} images mapped')
    if bad:
        sys.exit(1)
    if '--check' not in sys.argv:
        OUT.write_text(json.dumps({'built_from': 'build/image_names.py', 'status': 'map only; not switched', 'images': rows}, indent=1) + '\n')
        print('wrote', OUT.relative_to(ROOT))


if __name__ == '__main__':
    main()
