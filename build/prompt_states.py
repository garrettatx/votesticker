"""
Write docs/round3-prompts-one-state.md's per-state messages for every state that has no ready-made
set yet (no folder in site/assets/states/), from site/states.js. Headline and date line follow the
maker's own defaults, except early voting that has already started shows "BY <last day>".

    python3 -I build/prompt_states.py > /tmp/states.md
"""
import json
import re
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
TODAY = date(2026, 10, 19)
MON = 'JAN FEB MAR APR MAY JUN JUL AUG SEP OCT NOV DEC'.split()
POP = 'CA TX FL NY PA IL OH GA NC MI NJ VA WA AZ TN MA IN MD MO WI CO MN SC AL LA KY OR OK CT UT IA NV AR MS KS NM NE ID WV HI NH ME MT RI DE SD ND AK DC VT WY'.split()

# capital, outline note, state palette (background, ink, accent, name)
INFO = {
 'AL': ('Montgomery', '', ('#F3ECE1', '#5B1F2B', '#C98B2B', 'crimson and gold')),
 'AK': ('Juneau, in the southeast panhandle', 'Include the Aleutian chain and the southeast panhandle; keep it at true proportions, never squashed', ('#E6F0F6', '#0E2A47', '#F2B33D', 'night sky and gold')),
 'AZ': ('Phoenix', '', ('#FBEBDD', '#2E2A4F', '#D9663B', 'desert sunset')),
 'AR': ('Little Rock', '', ('#EEF2E6', '#1F3B2C', '#B5452B', 'pine and clay')),
 'CT': ('Hartford', '', ('#EAF0F5', '#13294B', '#7A9A3B', 'harbor and leaf')),
 'DE': ('Dover', 'Delaware is small and tall: keep the outline small and accurate', ('#EAF2F4', '#123C4A', '#D9A441', 'bay and buff')),
 'FL': ('Tallahassee', 'Include the panhandle at its true length; keep the outline small rather than squashing it', ('#E8F6F3', '#0B4F6C', '#F28F3B', 'gulf and citrus')),
 'GA': ('Atlanta', '', ('#F6EFE6', '#2B3A2E', '#E07A3F', 'peach and pine')),
 'HI': ('Honolulu, on Oahu', 'Show the main islands in their real arrangement, small; never a single blob', ('#E5F4F1', '#0D3B4C', '#E86A4A', 'reef and hibiscus')),
 'ID': ('Boise', '', ('#EEF1E8', '#22333B', '#8C6A3F', 'sage and timber')),
 'IL': ('Springfield, in the middle of the state, not Chicago', '', ('#EEF2F6', '#14213D', '#E5A823', 'lake and prairie gold')),
 'IN': ('Indianapolis', '', ('#F2F0E8', '#1C2541', '#C9A227', 'limestone and gold')),
 'IA': ('Des Moines', '', ('#F5F0E1', '#2B2D42', '#D99A26', 'corn gold')),
 'KY': ('Frankfort, not Louisville or Lexington', '', ('#EEF3EC', '#123524', '#3A6EA5', 'bluegrass')),
 'LA': ('Baton Rouge, not New Orleans', 'Keep the boot shape and the Mississippi delta accurate', ('#EFEDF5', '#2D1E4F', '#D4A72C', 'bayou purple and gold')),
 'ME': ('Augusta', '', ('#E8F1EE', '#123B33', '#3B6EA5', 'spruce and coast')),
 'MD': ('Annapolis, on the west shore of the Chesapeake Bay', 'Keep the long western panhandle and the bay shape accurate; Maryland is wide and thin, so keep the outline small above the headline', ('#F6F1E7', '#1C1C1C', '#E0A100', 'black and gold')),
 'MA': ('Boston', 'Include Cape Cod', ('#EDF1F5', '#16324F', '#B5502D', 'harbor and brick')),
 'MI': ('Lansing', 'Show both the Upper and Lower Peninsulas, with the gap between them', ('#E9F1F7', '#0B3954', '#2A9D8F', 'great lakes')),
 'MN': ('Saint Paul, in the east-central part of the state, not Minneapolis or Duluth', 'Include the small Northwest Angle notch at the top and the Arrowhead in the northeast', ('#EAF2F8', '#102A43', '#3C8D5A', 'lakes and pines')),
 'MS': ('Jackson', '', ('#EEF0EA', '#1F2E2B', '#B9852E', 'magnolia and river')),
 'MT': ('Helena', '', ('#EAF0F4', '#1D3557', '#C46D3B', 'big sky and rust')),
 'NE': ('Lincoln, not Omaha', '', ('#F4EFE3', '#2C2A29', '#C0392B', 'prairie and barn red')),
 'NV': ('Carson City, in the west near Lake Tahoe, not Las Vegas or Reno', '', ('#F6EEE6', '#22223B', '#C2703D', 'sagebrush and sandstone')),
 'NH': ('Concord', 'New Hampshire is tall and narrow: keep the outline small and set the headline beside or below it', ('#EEF2EE', '#1E3A2F', '#C8553D', 'granite and maple')),
 'NJ': ('Trenton, on the western edge along the Delaware River', 'New Jersey is tall and narrow: set the headline beside or below it, never inside', ('#F1ECE2', '#1B2A41', '#C9A227', 'shore and buff')),
 'NM': ('Santa Fe, not Albuquerque', '', ('#FBF0E1', '#3A2E39', '#D1495B', 'adobe and chile')),
 'ND': ('Bismarck', '', ('#F1F2EA', '#203038', '#C9A227', 'wheat and sky')),
 'OH': ('Columbus', '', ('#F1F1EE', '#1F2A44', '#B33F40', 'river and buckeye')),
 'OK': ('Oklahoma City', 'Include the panhandle at its true length', ('#F5EEE6', '#22303C', '#C46A2E', 'red earth')),
 'OR': ('Salem, in the northwest, not Portland', '', ('#E6EFE9', '#1F3B2C', '#2F6690', 'forest and river')),
 'RI': ('Providence', 'Rhode Island is tiny: show the outline small with Narragansett Bay, or leave it out', ('#E8F1F6', '#0F3550', '#E0A526', 'ocean state')),
 'SC': ('Columbia, in the middle of the state, not Charleston', '', ('#EAF0F4', '#16324F', '#3E7CB1', 'palmetto and indigo')),
 'SD': ('Pierre, in the middle of the state', '', ('#F3EFE5', '#2A2D34', '#5B8E7D', 'badlands and sage')),
 'TN': ('Nashville', 'Tennessee is very wide and thin: keep the outline small above the headline', ('#F3EEE6', '#1F2A44', '#C1663B', 'smoky mountain')),
 'UT': ('Salt Lake City', '', ('#FBEFE3', '#2B2D42', '#C8553D', 'red rock')),
 'VT': ('Montpelier', '', ('#EEF3EC', '#1E3B2C', '#C0392B', 'green mountain and maple')),
 'WV': ('Charleston, West Virginia', 'Keep both panhandles accurate', ('#EEF1EA', '#1F2E3D', '#D4A72C', 'mountain and gold')),
 'WI': ('Madison', 'Include the Door Peninsula', ('#EEF2F6', '#14213D', '#C9473A', 'lake and cherry')),
 'WY': ('Cheyenne, in the southeast corner', 'Wyoming is a plain rectangle: keep it bold and simple', ('#F2EFE6', '#2B2D42', '#B5652B', 'high plains')),
}


def sd(iso):
    d = date.fromisoformat(iso)
    return d, f'{MON[d.month - 1]} {d.day}'


def message(r):
    a = r['abbr']
    cap, note, (bg, ink, acc, pname) = INFO[a]
    if r['status'] != 'verified' or r.get('kind') == 'none':
        head, line = 'VOTE / NOV 3', 'ELECTION DAY'
        extra = f'{r["name"]} cards say Election Day, so never use the words "early" or "mail."'
    elif r['kind'] == 'mail':
        head, line = 'VOTE / BY MAIL', 'DUE ' + sd(r['end'])[1]
        extra = f'{r["name"]} mails every voter a ballot, so never use the word "early."'
    else:
        head = 'VOTE / EARLY'
        if r.get('dateLine'):
            line = r['dateLine']
        else:
            s, ss = sd(r['start'])
            e, es = sd(r['end'])
            if s <= TODAY:
                line = 'BY ' + es
            elif s.month == e.month:
                line = f'{ss}–{e.day}'
            else:
                line = f'{ss}–{es}'
        extra = ''
    parts = [f'{r["name"]}. Use these exact details in all 10 images, and nothing from earlier states: Headline: {head}. Date line: {line}. Capital star: {cap}.']
    if note:
        parts.append(note + '.')
    if extra:
        parts.append(extra)
    parts.append(f'State palette ({pname}): background {bg}, ink {ink}, accent {acc}.')
    parts.append('Make the 10, check them, then send the zip.')
    return ' '.join(parts), head, line


def main():
    rows = {}
    for l in (ROOT / 'site/states.js').read_text().splitlines():
        if l.strip().startswith('{"abbr"'):
            r = json.loads(l.strip().rstrip(','))
            rows[r['abbr']] = r
    # States whose first sets were too samey to keep as final (October 19): redo them; their current
    # images stay live until the new ones replace them.
    redo = {'MD', 'NJ'}
    have = {p.name.upper() for p in (ROOT / 'site/assets/states').iterdir() if p.is_dir()} - redo
    todo = [a for a in POP if a in rows and a not in have]
    groups = [('Verified Dates', lambda r: r['status'] == 'verified'),
              ('Dates Not Yet Verified (Election Day Message)', lambda r: r['status'] != 'verified')]
    out = ['### Already Built (Skip These)\n']
    for a in [a for a in POP if a in have]:
        k = len(list((ROOT / 'site/assets/states' / a.lower()).glob('*.jpg')))
        out.append(f'- ~~{rows[a]["name"]}~~ ({k} stickers live)')
    out.append(f'\nRedo (current sets look too much alike; they stay live until replaced): {", ".join(rows[a]["name"] for a in POP if a in redo)}.')
    out.append('')
    n = 0
    for title, test in groups:
        sel = [a for a in todo if test(rows[a])]
        out.append(f'### {title}\n')
        for a in sel:
            n += 1
            msg, head, line = message(rows[a])
            out.append(f'#### {n}. {rows[a]["name"]} ({head}, {line})\n\n```\n{msg}\n```\n')
    print('\n'.join(out))
    print(f'<!-- {n} states, {len(have)} already have sets: {", ".join(sorted(have))} -->')


if __name__ == '__main__':
    main()
