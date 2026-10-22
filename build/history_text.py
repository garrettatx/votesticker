#!/usr/bin/env python3
"""Check the sticker history page's words against its evidence and compile them for the site.

build/history.json holds the page's words (English and Spanish). Every paragraph, timeline entry, card
and chart row names the facts it rests on; build/history-facts.json holds those facts (source URL, the
sentence that supports the claim, the check date, a status). A fact that isn't `verified` can't be
cited, a chart number has to match its fact's number, and no text may carry an em dash or a TODO.

    python3 build/history_text.py --check   # validate only
    python3 build/history_text.py           # validate, then write site/history-data.php
    python3 build/history_text.py --draft   # local preview: unchecked facts and missing Spanish are
                                            # allowed and shown as "Being checked"; the build refuses it
"""
import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
PAGE = ROOT / 'build' / 'history.json'
FACTS = ROOT / 'build' / 'history-facts.json'
OUT = ROOT / 'site' / 'history-data.php'


def main():
    draft = '--draft' in sys.argv
    page = json.loads(PAGE.read_text())
    ev = json.loads(FACTS.read_text()) if FACTS.exists() else {}
    facts = {}
    for group in ('events', 'numbers', 'people', 'law', 'designs'):
        for f in ev.get(group, []):
            facts[f['id']] = f
    errors, pending = [], []

    def problem(msg, soft=False):
        (pending if draft and soft else errors).append(msg)

    def verified(i):
        return facts.get(i, {}).get('status') == 'verified'

    def cite(ids, where):
        urls = []
        for i in ids or []:
            if i not in facts:
                problem(f'{where}: no fact "{i}"', soft=True)
            elif not verified(i):
                problem(f'{where}: fact "{i}" is {facts[i].get("status")}, not verified', soft=True)
            else:
                urls.append(facts[i]['src'])
        return urls

    def text(pair, where):
        for lang in ('en', 'es'):
            s = (pair or {}).get(lang, '')
            if not s:
                problem(f'{where}: no {lang} text', soft=(lang == 'es'))
            elif '—' in s or 'TODO' in s:
                problem(f'{where} ({lang}): em dash or TODO')

    def attach(item, fact_id, where):
        got = cite([fact_id], where)
        if got:
            f = facts[fact_id]
            item.update(src=got[0], quote=f.get('quote', ''), evidence=f.get('evidence', item.get('evidence')))
        item['pending'] = not got

    for e in page['events']:
        where = f'event {e["id"]}'
        text(e['title'], where + ' title')
        text(e['body'], where + ' body')
        attach(e, e['fact'], where)
    page['events'].sort(key=lambda e: (e['year'], e.get('order', 0)))

    for key, c in page.get('charts', {}).items():
        where = f'chart {key}'
        text(c['title'], where + ' title')
        srcs = cite(c.get('facts'), where)
        for r in c['rows']:
            text(r['label'], where + ' row')
            got = cite([r['fact']], where)
            srcs += got
            r['pending'] = not got
            if got and facts[r['fact']].get('value') not in (None, r['value']):
                errors.append(f'{where}: {r["value"]} does not match fact {r["fact"]} ({facts[r["fact"]]["value"]})')
        if c.get('note'):
            text(c['note'], where + ' note')
        c['src'] = list(dict.fromkeys(srcs))
        c['pending'] = any(r['pending'] for r in c['rows'])

    for sec in page['sections']:
        where = f'section {sec["id"]}'
        text(sec['h'], where + ' heading')
        for row in sec.get('answer', []):
            text(row['q'], where + ' answer q')
            text(row['a'], where + ' answer a')
            row['src'] = cite(row.get('facts'), where + ' answer')
            row['pending'] = not all(verified(i) for i in row.get('facts', []))
        for n, card in enumerate(sec.get('cards', [])):
            text(card['title'], f'{where} card {n} title')
            text(card['body'], f'{where} card {n} body')
            attach(card, card['fact'], f'{where} card {n}')
        for n, sc in enumerate(sec.get('scenes', [])):
            text(sc['title'], f'{where} scene {n} title')
            text(sc['body'], f'{where} scene {n} body')
        for group in ('paras', 'after'):
            for n, p in enumerate(sec.get(group, [])):
                text(p, f'{where} {group} {n}')
                if p.get('h3'):
                    text(p['h3'], f'{where} {group} {n} subhead')
                p['src'] = cite(p.get('facts'), f'{where} {group} {n}')
                p['pending'] = not all(verified(i) for i in p.get('facts', []))

    # State summaries on the map: short rewordings of each state's verified sticker story (the state
    # facts files are their evidence), so they only need text in both languages.
    for abbr, tz in page.get('teasers', {}).items():
        text(tz, f'teaser {abbr}')
        text(tz.get('label'), f'teaser {abbr} label')

    used = [f for f in facts.values() if f.get('status') == 'verified']
    page['checked'] = max([f.get('checked', '') for f in used] + [ev.get('checked', '2026-10-22')])
    page['sources'] = {**ev.get('sources', {}), **page.get('sources', {})}
    page['draft'] = bool(draft and pending)

    if errors:
        print('\n'.join(errors))
        raise SystemExit(f'{len(errors)} problem(s) in build/history.json')
    if pending:
        print(f'DRAFT: {len(pending)} item(s) not checked yet, e.g. {pending[0]}')
    print(f'history: OK, {len(page["events"])} timeline entries, {len(page["sections"])} sections, {len(page.get("charts", {}))} charts')
    if '--check' in sys.argv:
        return
    blob = json.dumps(page, ensure_ascii=False).replace('\\', '\\\\').replace("'", "\\'")
    OUT.write_text("<?php\n// Written by build/history_text.py from build/history.json and build/history-facts.json. Do not edit.\nreturn json_decode('" + blob + "', true);\n")
    print(f'wrote {OUT.relative_to(ROOT)}' + (' (DRAFT: local preview only)' if page['draft'] else ''))


if __name__ == '__main__':
    main()
