# Usage: VOTE_TODAY=2026-11-02 python3 build/build_static.py && python3 -I scripts/qa_stale.py 2026-11-02 dist
# Ranges that are still open (Oct 5 through Nov 2) also print; read the list, then judge.
# Sentences that point forward ("by", "until", "through", "hasta"...) to a date already past on the build date.
import re, html, glob, sys, datetime
build = datetime.date.fromisoformat(sys.argv[1]); root = sys.argv[2]
EN = {m: i for i, m in enumerate(['january','february','march','april','may','june','july','august','september','october','november','december'], 1)}
ES = {m: i for i, m in enumerate(['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'], 1)}
fw = r'(?:by|until|through|before|starting|from|opens?|closes?|ends?|is open|are open|hasta(?: el)?|a más tardar(?: el)?|antes del|desde el|a partir del|cierra el|abre el|termina el)'
pen = re.compile(fw + r'\s+(?:[A-Z][a-z]+day,?\s+)?(' + '|'.join(m.capitalize() for m in EN) + r')\s+(\d{1,2})', re.I)
pes = re.compile(fw + r'\s+(?:(?:lunes|martes|miércoles|jueves|viernes|sábado|domingo)\s+)?(\d{1,2}) de (' + '|'.join(ES) + ')', re.I)
past = re.compile(r'\b(closed|ended|was|were|had to|went out|terminó|cerró|fue|fueron|tenía|tenían|se enviaron)\b', re.I)
n = 0
for f in sorted(glob.glob(root + '/*/index.html') + glob.glob(root + '/es/*/index.html') + [root + '/index.html', root + '/es/index.html']):
    if 'history' in f: continue
    t = open(f).read(); t = re.sub(r'<(script|table)\b.*?</\1>', '', t, flags=re.S)
    txt = re.sub(r'\s+', ' ', html.unescape(re.sub('<[^>]+>', ' ', t)))
    for s in set(re.split(r'(?<=[.?!])\s', txt)):
        if past.search(s): continue
        ds = [datetime.date(2026, EN[m.group(1).lower()], int(m.group(2))) for m in pen.finditer(s)] + [datetime.date(2026, ES[m.group(2).lower()], int(m.group(1))) for m in pes.finditer(s)]
        if any(d < build for d in ds):
            n += 1; print(f.replace(root + '/', '').replace('/index.html', ''), '|', s[:200])
print(n, 'stale sentences on', build)
