"""
Write one Codex prompt file per state into docs/codex-prompts/, in build order, from the Codex plan
table (incoming/codex/PLAN.md, messages checked against site/states.js on October 20), the outline
notes in build/prompt_states.py, the capital positions in site/state-shapes.js and the state colors
in site/app.js. Garrett gives Codex one file; the rules live in docs/codex-ready-made-brief.md.

    python3 -I build/codex_prompts.py
"""
import ast, json, re
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
OUT = ROOT / 'docs' / 'codex-prompts'
DONE = {'MS'}  # sets already made with Codex
# Strong existing sets (October 20): keep them and add a five-image pack of the sayings they lack.
# Each holds only its main message, and dated ones go stale after voting ends.
STRONG = {'TX', 'KS', 'MO', 'WA', 'DC', 'CO', 'PA', 'NY', 'CA'}

src = (ROOT / 'build' / 'prompt_states.py').read_text()
INFO = ast.literal_eval(re.search(r'^INFO = (\{.*?^\})', src, re.S | re.M).group(1))
EXTRA = {'CA': 'Sacramento', 'CO': 'Denver', 'DC': '', 'KS': 'Topeka', 'MO': 'Jefferson City',
         'NC': 'Raleigh', 'NY': 'Albany, not New York City', 'PA': 'Harrisburg, not Philadelphia or Pittsburgh',
         'TX': 'Austin', 'VA': 'Richmond', 'WA': 'Olympia, not Seattle'}
shapes = json.loads(re.search(r'VOTE_SHAPES=(\{.*\})', (ROOT / 'site' / 'state-shapes.js').read_text()).group(1))
colors = {k: v for k, v in re.findall(r"([A-Z]{2}): \[('[^\]]+')\]", (ROOT / 'site' / 'app.js').read_text())}

rows = []
for line in (ROOT / 'incoming' / 'codex' / 'PLAN.md').read_text().splitlines():
    m = re.match(r'\| (\d+) \| (.+?) \(([A-Z]{2})\) \| (\d+) \| .*? \| (.*?) \| (.*?) \| (.*?) \|', line)
    if m:
        rows.append(dict(order=int(m[1]), name=m[2], abbr=m[3], have=int(m[4]), status=m[5],
                         head=m[6].replace(' / ', ' / '), date=m[7]))

def plan(r):
    st, name = r['status'], r['name'].upper()
    up = 'WASHINGTON, DC' if r['abbr'] == 'DC' else name
    kind = 'mail' if st.startswith('Mail') else 'early' if st.startswith('In-person') else 'none'
    main = f"{r['head']}, date line {r['date']}"
    if kind == 'mail':
        main = f"VOTE / BY MAIL, date line DUE NOV 3"
    rng = re.search(r'([A-Z]{3} \d+)–([A-Z]{3} )?(\d+)', st)
    note = ''
    if kind == 'early' and rng:
        end = (rng[2] or rng[1].split()[0] + ' ') + rng[3]
        if 'upcoming' in st:
            note = (f"Early voting runs {rng[0]}. If you run this on or after {rng[1]}, the date line is "
                    f"BY {end} instead. ")
        note += (f"If you run this after {end}, early voting is over: use VOTE / NOV 3 with ELECTION DAY "
                 f"as the main message and keep only I VOTED / EARLY from the early sayings.")
    short = len(name) <= 8 and r['abbr'] != 'DC'
    slots = [f'Core Cream shape: {main}', f'Core Navy big type: {main}', f'Core Forest badge (no ring): {main}',
             f'Core shape in the state palette, reversed (ink color as the background): {main}',
             f'Creative: {main}', f'Creative: I VOTED, second line {up}', f'Creative: I VOTED, second line {up}']
    if kind == 'early':
        slots.append('Creative: I VOTED / EARLY, second line ' + up)
    elif kind == 'mail':
        slots.append('Creative: MY BALLOT / IS IN, second line ' + up)
    else:
        slots.append(f'Creative: I VOTED, second line {up}')
    slots.append('Creative: MAKE A / PLAN, second line ' + ('VOTE EARLY' if kind == 'early' else 'VOTE NOV 3'))
    slots.append(f'Creative: {name} / VOTES, second line NOV 3' if short else f'Creative: {main}')
    if r['abbr'] in STRONG:
        extra = ('Creative: I VOTED / EARLY, second line ' + up if kind == 'early'
                 else 'Creative: MY BALLOT / IS IN, second line ' + up if kind == 'mail'
                 else f'Creative: I VOTED, second line {up}')
        slots = [f'Creative, the house design (tilted I VOTED, cream on deep plum, outline above): I VOTED, second line {up}',
                 f'Creative: I VOTED, second line {up}', extra,
                 'Creative: MAKE A / PLAN, second line ' + ('VOTE EARLY' if kind == 'early' else 'VOTE NOV 3'),
                 f'Creative: {name} / VOTES, second line NOV 3' if short else f'Creative: I VOTED, second line {up}']
    never = {'early': 'VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN',
             'mail': 'VOTE EARLY, I VOTED EARLY',
             'none': 'VOTE EARLY, I VOTED EARLY, VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN'}[kind]
    return kind, note, slots, never

OUT.mkdir(exist_ok=True)
for p in OUT.glob('*.md'):
    p.unlink()
todo = [r for r in rows if r['abbr'] not in DONE]
index = []
for i, r in enumerate(todo, 1):
    a = r['abbr']; k = a.lower()
    kind, note, slots, never = plan(r)
    info = INFO.get(a)
    cap = info[0] if info else EXTRA.get(a, '')
    outline = info[1] if info else ''
    bg, ink, acc = [c.strip("' ") for c in colors[a].split(',')]
    sh = shapes[a]; cx = round(100 * sh['cap'][0] / sh['w']); cy = round(100 * sh['cap'][1] / sh['h'])
    star = (f"Capital: {cap}. Star at {cx}% across and {cy}% down the outline's bounding box "
            f"(site/state-shapes.js).") if cap else "No capital star (DC is a district)."
    body = f"""# {r['name']} ({a}): {'I VOTED Pack' if a in STRONG else 'Ready-Made Set'} for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/{k}/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop. Ask for the second
line at 40% of the headline's letter height in the first prompt of every image; an undersized date line was
the most common redraw on October 21. If you are stopped partway, everything saved is used: write each PNG
as soon as it is checked, and keep going in slot order.

## Facts

- Source: site/states.js. Status on October 20: {r['status']}.
- {note or 'Main message: ' + slots[0].split(': ', 1)[1] + '.'}
- {star}
- Outline: {outline or 'accurate, at true proportions, never stretched.'}
- State palette: background {bg}, ink {ink}, accent {acc}. Core 4 reverses it: {ink} background.
- Ready-made images on the site now: {r['have']}. {'Those stay: they are strong, but every one carries the main message only. This pack adds the five sayings the set lacks, in new concepts and palettes that differ from the existing images (look at site/assets/states/' + k + '/ first).' if a in STRONG else 'This set adds ten; Claude decides what stays.'}

## The {'Five' if a in STRONG else 'Ten'} Images (slash = line break, not printed)

""" + '\n'.join(f'{n}. {s}' for n, s in enumerate(slots, 1)) + f"""

Never use: {never}.

## Output

- incoming/codex/{k}/BRIEFS.md before drawing (words, concept, colors, map and star position).
- {'Five' if a in STRONG else 'Ten'} PNGs named {k}-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/{k}/CHECK.md, then stop.
"""
    fn = f'{i:02d}-{k}-{r["name"].lower().replace(" ", "-")}.md'
    if a in STRONG:  # Garrett, October 20: the strong early sets stay as they are, no new images
        index.append(f'| {i} | {r["name"]} | {r["have"]} | {kind} | skip: strong set already |')
        continue
    (OUT / fn).write_text(body)
    index.append(f'| {i} | [{r["name"]}]({fn}) | {r["have"]} | {kind} | {"5-image I VOTED pack" if a in STRONG else "set of 10"} |')
(OUT / 'README.md').write_text("""# Codex Prompt Files

One file per state, in build order (fewest ready-made images first). Generated by
`build/codex_prompts.py`; rerun it after `site/states.js` changes. Mississippi is done.

**How to run one:** open a new chat in the Votesticker project (GPT-6.1 Sol, High) and send:

> Read docs/codex-prompts/<file> and follow it.

Two or three chats at a time at most. Tell Claude when a CHECK.md lands; Claude reviews, fixes,
and puts the approved set live.

| # | State | Images now | Kind | Makes |
|---|---|---|---|---|
""" + '\n'.join(index) + '\n')
print(len(todo), 'prompt files')
