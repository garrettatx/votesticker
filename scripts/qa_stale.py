# Usage: VOTE_TODAY=2026-11-02 python3 build/build_static.py && python3 -I scripts/qa_stale.py 2026-11-02 dist
# Finds text that points forward ("by", "until", "through", "hasta"...) to a date already past on the build date.
# Checks clause by clause (pass 5, Oct 31): sentences are split at commas, semicolons, colons and "and"/"but"
# ("y"/"pero"), so a past-tense clause ("Applications closed Oct 27") no longer hides a stale one next to it
# ("register when you vote early through November 2").
# STALE lines fail the run (exit 1). WINDOW lines are start dates ("from", "since", "desde", "opens") already
# past while voting is still open; they print for reading, not failure. After Election Day they count as STALE.
import re, html, glob, sys, datetime
build = datetime.date.fromisoformat(sys.argv[1]); root = sys.argv[2]
ELECTION = datetime.date(2026, 11, 3)
EN = {m: i for i, m in enumerate(['january','february','march','april','may','june','july','august','september','october','november','december'], 1)}
ES = {m: i for i, m in enumerate(['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'], 1)}
start_kw = r'from|starting|opens?|since|desde(?: el)?|a partir del|abre el'
end_kw = r'by|until|through|before|to|closes?|ends?|is open|are open|hasta(?: el)?|a más tardar(?: el)?|antes del|al|cierra el|termina el'
wd_en = r'(?:(?:monday|tuesday|wednesday|thursday|friday|saturday|sunday),?\s+)?'
wd_es = r'(?:(?:lunes|martes|miércoles|jueves|viernes|sábado|domingo)\s+)?'
pen = re.compile(r'\b(' + start_kw + '|' + end_kw + r')\s+' + wd_en + '(' + '|'.join(EN) + r')\s+(\d{1,2})\b', re.I)
pes = re.compile(r'\b(' + start_kw + '|' + end_kw + r')\s+' + wd_es + r'(\d{1,2}) de (' + '|'.join(ES) + r')\b', re.I)
ren = re.compile(r'\b(' + '|'.join(EN) + r')\s+(\d{1,2})\s*(?:to|through|–|-)\s*(?:(' + '|'.join(EN) + r')\s+)?(\d{1,2})\b', re.I)
res = re.compile(r'\bdel\s+(\d{1,2})(?:\s+de\s+(' + '|'.join(ES) + r'))?\s+al\s+(\d{1,2})\s+de\s+(' + '|'.join(ES) + r')\b', re.I)
is_start = re.compile(r'^(?:' + start_kw + r')$', re.I)
# Past tense in the same clause means the date is history, not an instruction.
past = re.compile(r"\b(closed|ended|ran|began|came|sent|was|were|had|went out|reached|mailed|(?:who|you|they) registered|updated|"
                  r"didn't|did not|missed|terminó|cerró|fue|fueron|tenía|tenías|tenían|había|habías|se enviaron)\b"
                  r"|\b\w+(?:ó|aste|aron|ieron)\b|\b(?!existe\b|insiste\b)\w+iste\b", re.I)
# Clause breaks. A comma right after a weekday ("Tuesday, November 3") or before a year ("November 3, 2026") is
# part of the date, not a break.
split = re.compile(r'(?<!day),\s+(?!\d{4})|[;:]\s+|\s+(?:and|but|y|pero)\s+', re.I)
stale = []; window = []
for f in sorted(glob.glob(root + '/*/index.html') + glob.glob(root + '/es/*/index.html') + [root + '/index.html', root + '/es/index.html']):
    if 'history' in f: continue
    page = f.replace(root + '/', '').replace('/index.html', '').replace(root, '') or 'home'
    t = open(f).read(); t = re.sub(r'<(script|table)\b.*?</\1>', '', t, flags=re.S)
    txt = re.sub(r'\s+', ' ', html.unescape(re.sub('<[^>]+>', ' ', t)))
    seen = set()
    for s in re.split(r'(?<=[.?!])\s', txt):
        for c in split.split(s):
            if c in seen or past.search(c): continue
            seen.add(c)
            hits = [(m.group(1), datetime.date(2026, EN[m.group(2).lower()], int(m.group(3)))) for m in pen.finditer(c)]
            hits += [(m.group(1), datetime.date(2026, ES[m.group(3).lower()], int(m.group(2)))) for m in pes.finditer(c)]
            # A range ("October 29 to 31", "del 17 al 30 de octubre") is judged by its end date: a start date
            # inside a range still open is a WINDOW, and a range that has ended is STALE.
            for m in ren.finditer(c):
                a = datetime.date(2026, EN[m.group(1).lower()], int(m.group(2)))
                hits.append(('from', a)); hits.append(('through', datetime.date(2026, EN[(m.group(3) or m.group(1)).lower()], int(m.group(4)))))
            for m in res.finditer(c):
                b = ES[m.group(4).lower()]
                hits.append(('desde', datetime.date(2026, ES[m.group(2).lower()] if m.group(2) else b, int(m.group(1))))); hits.append(('al', datetime.date(2026, b, int(m.group(3)))))
            ranged = {d for m in list(ren.finditer(c)) for d in [datetime.date(2026, EN[m.group(1).lower()], int(m.group(2)))]}
            ranged |= {datetime.date(2026, ES[m.group(2).lower()] if m.group(2) else ES[m.group(4).lower()], int(m.group(1))) for m in res.finditer(c)}
            hits = [(k, d) for k, d in hits if not (d in ranged and not is_start.match(k))]
            gone = [(k, d) for k, d in hits if d < build]
            if not gone: continue
            if build <= ELECTION and all(is_start.match(k) for k, _ in gone):
                window.append(f'{page} | {c[:200]}')
            else:
                stale.append(f'{page} | {c[:200]}  [in: {s[:160]}]' if c != s else f'{page} | {c[:200]}')
for w in window: print('WINDOW', w)
for s in stale: print('STALE ', s)
print(len(stale), 'stale clauses,', len(window), 'open-window clauses on', build)
sys.exit(1 if stale else 0)
