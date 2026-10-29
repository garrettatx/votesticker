"""Apply verified FAQ answer changes to build/faq_text.py and their sources to docs/faq-sources.json.

    python3 -I scripts/faq_edit.py edits.json      # from the votesticker repo root

edits.json is a list of objects, one per answer:
    {"st": "MO", "key": "id", "src": "https://...", "en": "...", "es": "..."}                 replace
    {"st": "UT", "key": "reg", "src": "https://...", "append_en": "...", "append_es": "..."}  add a sentence
    {"st": "FL", "key": "reg", "src": "https://...", "en": "...", "es": "...",
     "until": "2026-10-30", "after_en": "...", "after_es": "..."}                            dated answer
Keys are polls, mail, id, reg, selfie. A key the state doesn't have yet is added. Run build/faq_text.py after.
"""
import json
import re
import sys

KEYMAP = {'polls': 'polls', 'mail': 'polls', 'id': 'id', 'reg': 'registration', 'selfie': 'selfie'}


def entry(key, en, es, until=None, after_en=None, after_es=None):
    pad = ' ' * (len(key) + 13)
    out = f"        '{key}': ({en!r},\n{pad}{es!r}"
    if until:
        out += f",\n{pad}{{'until': {until!r}, 'en': {after_en!r},\n{pad} 'es': {after_es!r}}}"
    return out + '),\n'


def main(path):
    edits = json.load(open(path))
    p = 'build/faq_text.py'
    s = open(p).read()
    for e in edits:
        i = s.index(f"    '{e['st']}': {{")
        j = s.index('    },\n', i)
        block = s[i:j]
        key = e['key']
        # One answer: a 2- or 3-item tuple (the third is the dated after-text), or a no_reg(...) call.
        m = re.search(rf"        '{key}': (\(.*?\)|no_reg\(.*?\)),\n(?=        '|$)", block, re.S)
        if 'append_en' in e:
            old = re.match(r"\((\".*?\"|'.*?'),\s*(\".*?\"|'.*?')", m.group(1), re.S)
            new = entry(key, eval(old.group(1)) + ' ' + e['append_en'], eval(old.group(2)) + ' ' + e['append_es'])
        else:
            new = entry(key, e['en'], e['es'], e.get('until'), e.get('after_en'), e.get('after_es'))
        block = block[:m.start()] + new + block[m.end():] if m else block + new
        s = s[:i] + block + s[j:]
    open(p, 'w').write(s)
    p = 'docs/faq-sources.json'
    d = json.load(open(p))
    for e in edits:
        v = d[e['st']].get(KEYMAP[e['key']]) or {}
        v['source'] = e['src']
        v['checked'] = e.get('checked', '2026-10-28')
        d[e['st']][KEYMAP[e['key']]] = v
    json.dump(d, open(p, 'w'), indent=1, ensure_ascii=False)
    open(p, 'a').write('\n')
    print(len(edits), 'edits')


if __name__ == '__main__':
    main(sys.argv[1])
