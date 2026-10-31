"""Sticker collection data (docs/sticker-archive-spec.md), October 25, 2026. Data only: no page yet.

Writes build/sticker-collection.json from two sources:
- Our ready-made designs: every site/assets/states/<abbr>/*.jpg, its alt text (alts.json) and the message
  it carries, read from the alt the same way index.php vote_file_current() reads it.
- Election office records: each state's verified sticker facts (build/state-facts/<abbr>.json,
  sticker.tradition, .early, .mail), with the official source, quote and checked date carried over.
  Designer, issuing office, selection and permission are filled in by hand in
  build/sticker-offices.json, keyed by record id; the build never guesses them.

  python3 -I build/sticker_collection.py           # write the file
  python3 -I build/sticker_collection.py --check   # validate only (exit 1 on a problem)
"""
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
STATES = ROOT / 'site' / 'assets' / 'states'
FACTS = ROOT / 'build' / 'state-facts'
HAND = ROOT / 'build' / 'sticker-offices.json'
OUT = ROOT / 'build' / 'sticker-collection.json'

# Hand-kept fields an office record may carry (spec, Record Fields). Anything else in the hand file is an error.
HAND_FIELDS = {'title', 'county', 'year', 'designer', 'office', 'selection', 'elections', 'image_permission', 'permission_note', 'reviewed', 'split'}
USES = {'early': 'early voting', 'mail': 'mail packet'}
PERMISSIONS = {'none', 'confirmed', 'license'}


def message(alt):
    """already-voted, planning or deadline-reminder, from the words the sticker shows."""
    m = re.search(r'reading (.*)$', alt or '', re.I)
    says = (m.group(1) if m else '').lower()
    if re.match(r'(i voted|my ballot is in|first-time voter|ya voté|yo voté|mi primer voto)', says):
        return 'already-voted', says
    if re.match(r'[a-z. ]+ votes\b', says):  # "Ohio votes, Nov 3 2026": a state's own election, true after it too
        return 'planning', says
    if re.search(r'\b(oct|nov|by|through)\b|\d', says):
        return 'deadline-reminder', says
    return 'planning', says


def ours(alts):
    rows = []
    for d in sorted(p for p in STATES.iterdir() if p.is_dir()):
        for f in sorted(d.glob('*.jpg'), key=lambda p: [int(t) if t.isdigit() else t for t in re.split(r'(\d+)', p.stem)]):
            alt = alts.get(f.stem, '')
            kind, says = message(alt)
            rows.append({
                'id': f.stem, 'state': d.name.upper(), 'county': None, 'origin': 'votesticker',
                'title': says, 'alt': alt, 'image': f'assets/states/{d.name}/{f.name}',
                'message': kind, 'year': 2026, 'elections': ['2026 November general'],
                # Dated reminders leave the active gallery after their date; the collection keeps them.
                # Same test as index.php vote_file_current() after Election Day.
                'placement': 'both' if re.match(r'(i voted|my ballot is in|first-time voter|future voter|ya voté|yo voté|mi primer voto|yo también votaré|[a-z. ]+ votes\b)', says) else 'collection',
            })
    return rows


def office_row(rid, abbr, story, dist):
    return {
        'id': rid, 'state': abbr, 'county': None, 'origin': 'office', 'title': None,
        'designer': None, 'office': None, 'selection': None, 'year': None, 'elections': None,
        'story': story, 'distribution': dist,
        # "Published" on an official site is not permission (spec, Rules): no image until a grant is recorded.
        'image': None, 'image_permission': 'none', 'permission_note': None,
        'placement': 'collection', 'reviewed': False,
    }


def offices(hand):
    rows = []
    for f in sorted(FACTS.glob('*.json')):
        abbr = f.stem.upper()
        st = json.loads(f.read_text()).get('sticker', {})
        trad = st.get('tradition') or {}
        rid = f'{f.stem}-office'
        # A pickup fact about a different sticker (another county's, say) becomes its own record,
        # <state>-<use>, when the hand record lists that use under "split".
        split = set(hand.get(rid, {}).get('split', []))
        dist, own = [], []
        for use, label in USES.items():
            fact = st.get(use) or {}
            if fact.get('status') == 'verified':
                ev = {'use': label, 'election': '2026 November general',
                      'en': fact.get('en'), 'src': fact.get('src'), 'quote': fact.get('quote'), 'checked': fact.get('checked')}
                (own if use in split else dist).append((use, ev))
        story = {k: trad.get(k) for k in ('en', 'es', 'src', 'quote', 'checked')} if trad.get('status') == 'verified' else None
        if story or dist:
            row = office_row(rid, abbr, story, [ev for _, ev in dist])
            row.update({k: v for k, v in hand.get(rid, {}).items() if k != 'split'})
            rows.append(row)
        for use, ev in own:
            row = office_row(f'{f.stem}-{use}', abbr, None, [ev])
            row.update(hand.get(row['id'], {}))
            rows.append(row)
    return rows


def check(rows, hand):
    bad = []
    ids = [r['id'] for r in rows]
    bad += [f'duplicate id {i}' for i in sorted({i for i in ids if ids.count(i) > 1})]
    known = {r['id'] for r in rows if r['origin'] == 'office'}
    # A record whose every fact is split out has nothing left but its split list.
    known |= {rid for rid, fields in hand.items() if set(fields) == {'split'}}
    for rid, fields in hand.items():
        if rid not in known:
            bad.append(f'{rid}: hand record has no verified facts behind it')
        bad += [f'{rid}: unknown field {k}' for k in fields if k not in HAND_FIELDS]
        bad += [f'{rid}: split use {u!r} is not early or mail' for u in fields.get('split', []) if u not in USES]
    for r in rows:
        if r['origin'] == 'votesticker':
            if not r['alt']:
                bad.append(f"{r['id']}: no alt text")
            if not (ROOT / 'site' / r['image']).is_file():
                bad.append(f"{r['id']}: image missing")
            continue
        if r['image_permission'] not in PERMISSIONS:
            bad.append(f"{r['id']}: image_permission {r['image_permission']!r}")
        if r['image_permission'] != 'none' and not r.get('permission_note'):
            bad.append(f"{r['id']}: permission without the grant or license line")
        for ev in ([r['story']] if r['story'] else []) + r['distribution']:
            if not (ev.get('src') and ev.get('quote') and ev.get('checked')):
                bad.append(f"{r['id']}: evidence without source, quote and checked date")
    return bad


def main():
    alts = json.loads((STATES / 'alts.json').read_text())
    hand = {k: v for k, v in (json.loads(HAND.read_text()) if HAND.is_file() else {}).items() if not k.startswith('_')}
    rows = ours(alts) + offices(hand)
    bad = check(rows, hand)
    for b in bad:
        print('FAIL', b)
    n_ours = sum(r['origin'] == 'votesticker' for r in rows)
    n_off = len(rows) - n_ours
    print(f'{n_ours} VoteSticker designs, {n_off} election office records '
          f'({sum(r["reviewed"] for r in rows if r["origin"] == "office")} reviewed)')
    if bad:
        sys.exit(1)
    if '--check' not in sys.argv:
        OUT.write_text(json.dumps({'built_from': 'build/sticker_collection.py', 'records': rows}, indent=1, ensure_ascii=False) + '\n')
        print('wrote', OUT.relative_to(ROOT))


if __name__ == '__main__':
    main()
