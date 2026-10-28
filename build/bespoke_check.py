"""Check the bespoke sticker sets (scripts/render_bespoke.mjs, October 26) against the state rules.

The build runs this; any problem stops it. Rules:
- every manifest entry (site/assets/states/bespoke.json) has its file and English alt text; Spanish and
  shared stickers also have Spanish alt text ("<stem>@es"), and every bespoke file is in the manifest;
- I voted early / Ya voté temprano only where the state has verified in-person early voting, I voted by
  mail / Ya voté por correo only in verified all-mail states (site/states.js);
- Spanish messages only on Spanish or shared stickers, English messages only on English ones;
- fourteen per state (eleven, plus three added October 27), all different designs, never Shadow;
- six different colorways on each page: the English six and the Spanish six (the shared YA VOTÉ counts on both);
- file names lowercase-hyphen, starting with the state's name, 80 characters or fewer.

  python3 -I build/bespoke_check.py
"""
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
STATES = ROOT / 'site' / 'assets' / 'states'
EARLY = {'voted', 'yavotetemprano'}
MAIL = {'mailed', 'yavotecorreo'}
SPANISH = {'yavote', 'miprimervoto', 'yotambien', 'yavotetemprano', 'yavotecorreo'}


def slug(s):
    return re.sub(r'[^a-z]+', '-', s.lower()).strip('-')


def main():
    rows = {}
    for line in (ROOT / 'site' / 'states.js').read_text().splitlines():
        line = line.strip().rstrip(',')
        if line.startswith('{"abbr"'):
            r = json.loads(line)
            rows[r['abbr'].lower()] = r
    m = json.loads((STATES / 'bespoke.json').read_text())
    alts = json.loads((STATES / 'alts.json').read_text())
    bad, per = [], {}
    for stem, b in m.items():
        abbr = next((k for k, r in rows.items() if stem.startswith(slug(r['name']) + '-')), None)
        if not abbr:
            bad.append(f'{stem}: no state name at the start')
            continue
        st = rows[abbr]
        per.setdefault(abbr, []).append(b)
        if not (STATES / abbr / f'{stem}.jpg').is_file():
            bad.append(f'{stem}: file missing')
        if not alts.get(stem):
            bad.append(f'{stem}: no alt text')
        if b['lang'] in ('es', 'both') and not alts.get(stem + '@es'):
            bad.append(f'{stem}: no Spanish alt text')
        verified = st.get('status') == 'verified'
        if b['headline'] in EARLY and not (verified and st.get('kind') == 'in-person'):
            bad.append(f'{stem}: says voted early, but {st["name"]} has no verified early voting')
        if b['headline'] in MAIL and not (verified and st.get('kind') == 'mail'):
            bad.append(f'{stem}: says by mail, but {st["name"]} is not an all-mail state')
        if (b['headline'] in SPANISH) != (b['lang'] in ('es', 'both')):
            bad.append(f'{stem}: message {b["headline"]} in a {b["lang"]} sticker')
        if b['design'] == 'shadow':
            bad.append(f'{stem}: Shadow is retired for bespoke sets')
        if not re.fullmatch(r'[a-z0-9]+(-[a-z0-9]+)*', stem) or len(stem) > 80:
            bad.append(f'{stem}: file name not lowercase-hyphen or over 80 characters')
    for abbr, r in rows.items():
        got = per.get(abbr, [])
        if len(got) != 14:
            bad.append(f'{r["name"]}: {len(got)} bespoke stickers, want 14')
        if len({b['design'] for b in got}) != len(got):
            bad.append(f'{r["name"]}: a design repeats in its set')
        for lang in ('en', 'es'):
            pals = [b['palette'] for b in got if b['lang'] in (lang, 'both')]
            if len(set(pals)) != len(pals):
                bad.append(f'{r["name"]}: {lang} set repeats a colorway ({", ".join(sorted(pals))})')
        files = {f.stem for f in (STATES / abbr).glob(slug(r['name']) + '-*.jpg')}
        bad += [f'{s}: file not in the manifest' for s in sorted(files - set(m))]
    for b in bad:
        print('FAIL', b)
    print(f'{len(m)} bespoke stickers, {len(per)} states')
    if bad:
        sys.exit(1)


if __name__ == '__main__':
    main()
