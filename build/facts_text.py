"""
State voting facts for the guide sections on each state page (dates table, where to vote, voter ID,
mail voting, rules at the polls, the state's sticker tradition, extra FAQ answers).

One JSON file per state in build/state-facts/<abbr>.json, tagged with the election it describes. Every
fact carries its official source URL, the date it was read, and the sentence it rests on, so the page
can link each section to its source and next season's update starts from a list of what to re-check.
Plan and file format: docs/content-strategy-state-pages.md (Section 7).

    python3 -I build/facts_text.py --check [abbr ...]   # validate, print problems, exit 1 on any
    python3 -I build/facts_text.py                      # validate all, then write site/facts-states.php

A state with no file renders exactly as before. A file reaches the site only once it is reviewed and
marked "live": true at the top; until then --check still validates it. A fact with "status": "missing" is left off the page.
New election season: copy the files, set ELECTION below and each file's "election", and every fact
whose "checked" date is older than the season start fails until it is read again.
"""
import json
import re
import sys
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DIR = ROOT / 'build' / 'state-facts'
OUT = ROOT / 'site' / 'facts-states.php'

ELECTION = '2026-11-03'
SEASON_START = date(2026, 9, 1)     # facts read before this belong to an earlier election
MAX_AGE_DAYS = 21                   # a fact older than this at build time must be read again

# Official hosts a source may come from. A state election office, a state legislature or code site,
# or a county or city election office (.gov or a state's own domain). Never aggregators or news.
BANNED_HOSTS = ('vote.org', 'vote411.org', 'ballotpedia.org', 'ncsl.org', 'wikipedia.org', 'cbsnews.com',
                'nytimes.com', 'usatoday.com', 'aarp.org', 'reddit.com', 'chatgpt.com')
OFFICIAL = re.compile(r'(\.gov$|\.us$|^sos\.|^elections?\.|^vote|statutes|legis|^www\.sos\.|capitol)')
BANNED_WORDS = re.compile(r'RE-VERIFY|\bTODO\b|(?i:\blikely\b|\bprobably\b|\btypically\b|\bgenerally\b|\busually\b)|—')
# State election offices that run tools on a vendor's host, linked from the state's own page.
ALLOW_HOSTS = {'goelect.txelections.civixapps.com',
               'dcboe.org', 'www.dcboe.org', 'earlyvoting.dcboe.org',
               'nmonesource.com'}  # New Mexico's official statutes (NMSA), published by the NM Compilation Commission  # DC Board of Elections, the District's election office  # Texas My Voter Portal, linked from votetexas.gov/voting/where.html

# Every fact the template knows, with its kind: text (en/es strings), list (en/es lists), url (a bare
# string), rule (one of RULES).
FACTS = {
    'dates.reg_deadline': 'text', 'dates.mail_request': 'text', 'dates.mail_return': 'text',
    'dates.early_hours': 'text', 'dates.cure': 'text',
    'how.in_person': 'text', 'how.first_time': 'text',
    'where.locator_url': 'url', 'where.locator_label': 'label', 'where.locator_cta': 'label', 'where.local_offices_url': 'url',
    'where.sample_ballot_url': 'url', 'where.rule': 'text',
    'id.rule': 'rule', 'id.accepted': 'list', 'id.expired': 'text', 'id.without': 'text',
    'mail.who': 'text', 'mail.who_list': 'list', 'mail.track_url': 'url', 'mail.cure': 'text', 'mail.dropbox': 'text', 'mail.witness': 'text',
    'polls_rules.gear': 'text', 'polls_rules.phone': 'text', 'polls_rules.line': 'text',
    'sticker.tradition': 'text', 'sticker.early': 'text', 'sticker.mail': 'text',
    'faq.provisional': 'text', 'faq.line': 'text', 'faq.track': 'text', 'faq.unsigned': 'text', 'faq.late_mail': 'text',
}
RULES = ('photo_required', 'photo_requested', 'id_nonphoto', 'no_id')


def get(d, path):
    for k in path.split('.'):
        if not isinstance(d, dict) or k not in d:
            return None
        d = d[k]
    return d


def check(abbr, d, today):
    errs = []
    if d.get('abbr') != abbr.upper():
        errs.append(f'abbr is {d.get("abbr")!r}, file is {abbr}')
    if d.get('election') != ELECTION:
        errs.append(f'election is {d.get("election")!r}, this season is {ELECTION}')
    known = set(FACTS)
    for group, body in d.items():
        if group in ('sources', 'live'):
            continue
        if isinstance(body, dict):
            for k in body:
                if f'{group}.{k}' not in known:
                    errs.append(f'unknown fact {group}.{k}')
    for path, kind in FACTS.items():
        v = get(d, path)
        if v is None or v == '':
            continue
        if kind == 'label':
            # A label is shown as-is: a string, or {"en": ..., "es": ...} for a name that reads differently in Spanish.
            if not (isinstance(v, str) or (isinstance(v, dict) and v.get('en') and v.get('es') and set(v) <= {'en', 'es'})):
                errs.append(f'{path}: must be a string or an object with en and es')
            continue
        if kind == 'url':
            if not isinstance(v, str):
                errs.append(f'{path}: must be a string')
            else:
                errs += [f'{path}: {e}' for e in host_errors(v)]
            continue
        if kind == 'rule':
            if v not in RULES:
                errs.append(f'{path}: {v!r} is not one of {RULES}')
            continue
        if not isinstance(v, dict):
            errs.append(f'{path}: must be an object'); continue
        st = v.get('status')
        if st == 'missing':
            extra = set(v) - {'status', 'notes'}
            if extra:
                errs.append(f'{path}: a missing fact has only status (and notes), found {sorted(extra)}')
            continue
        if st != 'verified':
            errs.append(f'{path}: status must be verified or missing, not {st!r}'); continue
        en = v.get('en')
        if kind == 'list':
            if not isinstance(en, list) or not en or not all(isinstance(x, str) and x for x in en):
                errs.append(f'{path}: en must be a non-empty list of strings')
            es = v.get('es', [])
            if es and len(es) != len(en or []):
                errs.append(f'{path}: es has {len(es)} items, en has {len(en or [])}')
            texts = (en or []) + (es or [])
        else:
            if not isinstance(en, str) or len(en) < 12:
                errs.append(f'{path}: en is missing or too short')
            texts = [en or '', v.get('es', ''), v.get('en_after', ''), v.get('es_after', '')]
            if v.get('until'):
                try:
                    date.fromisoformat(v['until'])
                except ValueError:
                    errs.append(f'{path}: until must be YYYY-MM-DD')
                if not v.get('en_after'):
                    errs.append(f'{path}: until needs en_after (what the page says once the date passes)')
        for t in texts:
            m = BANNED_WORDS.search(t)
            if m:
                errs.append(f'{path}: banned word or mark {m.group(0)!r}')
        errs += [f'{path}: {e}' for e in host_errors(v.get('src', ''))]
        if not v.get('quote') or len(v['quote']) < 12:
            errs.append(f'{path}: quote (the source sentence) is required')
        try:
            c = date.fromisoformat(v.get('checked', ''))
            if c < SEASON_START:
                errs.append(f'{path}: checked {c} is before this season ({SEASON_START}); read it again')
            elif (today - c).days > MAX_AGE_DAYS:
                errs.append(f'{path}: checked {c} is more than {MAX_AGE_DAYS} days old; read it again')
            elif c > today:
                errs.append(f'{path}: checked {c} is in the future')
        except ValueError:
            errs.append(f'{path}: checked must be YYYY-MM-DD')
    # Every source host needs a readable name in both languages ("the Texas Secretary of State").
    names = d.get('sources') or {}
    for path, kind in FACTS.items():
        v = get(d, path)
        src = v.get('src') if isinstance(v, dict) else None
        if src:
            host = re.sub(r'^https://([^/]+).*', r'\1', src).lower()
            n = names.get(host) or names.get(host.removeprefix('www.'))
            if not isinstance(n, dict) or not n.get('en') or not n.get('es'):
                errs.append(f'{path}: add sources[{host!r}] with en and es names')
    if get(d, 'where.locator_url') and not get(d, 'where.locator_label'):
        errs.append('where.locator_label is required with locator_url')
    return errs


def host_errors(url):
    if not url:
        return ['src is required']
    m = re.match(r'https://([^/]+)/', url + '/')
    if not m:
        return [f'{url}: must be an https:// URL']
    host = m.group(1).lower()
    if host in ALLOW_HOSTS:
        return []
    if any(host == b or host.endswith('.' + b) for b in BANNED_HOSTS):
        return [f'{host} is not an official source']
    if not OFFICIAL.search(host.removeprefix('www.')) and not OFFICIAL.search(host):
        return [f'{host} does not look like an official election or legislature host; add it to OFFICIAL if it is']
    return []


def php(v, ind=1):
    t = '\t' * ind
    if isinstance(v, dict):
        if not v:
            return 'array()'
        return 'array(\n' + ''.join(f"{t}\t{php(k)} => {php(x, ind + 1)},\n" for k, x in v.items()) + t + ')'
    if isinstance(v, list):
        return 'array( ' + ', '.join(php(x, ind) for x in v) + ' )'
    if isinstance(v, bool):
        return 'true' if v else 'false'
    return "'" + str(v).replace('\\', '\\\\').replace("'", "\\'") + "'"


def load(abbrs=None):
    files = sorted(DIR.glob('*.json'))
    if abbrs:
        files = [DIR / f'{a.lower()}.json' for a in abbrs]
    return {f.stem: json.loads(f.read_text()) for f in files}


def main():
    args = sys.argv[1:]
    only_check = '--check' in args
    abbrs = [a for a in args if not a.startswith('--')]
    today = date.today()
    data, bad = load(abbrs or None), 0
    for abbr, d in data.items():
        errs = check(abbr, d, today)
        for e in errs:
            print(f'{abbr.upper()}: {e}')
        bad += len(errs)
        if not errs and only_check:
            n = sum(1 for p, k in FACTS.items() if k not in ('url', 'label', 'rule') and (get(d, p) or {}).get('status') == 'verified')
            print(f'{abbr.upper()}: OK, {n} verified facts')
    if bad:
        print(f'{bad} problem(s)'); sys.exit(1)
    if only_check:
        return
    # Keep verified facts only; the template never sees a missing one.
    out = {}
    for abbr, d in data.items():
        if d.get('live') is not True:  # only files reviewed and marked "live": true reach the site
            continue
        st = {'election': d['election'], 'sources': d.get('sources') or {}}
        for path, kind in FACTS.items():
            v = get(d, path)
            if v in (None, ''):
                continue
            if kind in ('url', 'label', 'rule'):
                st[path] = v
            elif v.get('status') == 'verified':
                st[path] = {k: v.get(k, '' if kind == 'text' else []) for k in ('en', 'es', 'src', 'checked')}
                st[path].update({k: v[k] for k in ('until', 'en_after', 'es_after') if v.get(k)})
        out[abbr.upper()] = st
    OUT.write_text('<?php\n// Generated by build/facts_text.py from build/state-facts/*.json. Edit the JSON, not this file.\nreturn '
                   + php(out, 0) + ';\n')
    print(f'{OUT.relative_to(ROOT)}: {len(out)} live state(s); waiting for review: {sorted(a.upper() for a, d in data.items() if d.get("live") is not True) or "none"}')


if __name__ == '__main__':
    main()
