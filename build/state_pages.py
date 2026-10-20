"""
Write one folder per state (site/<slug>/index.php) from site/states.js.

Every state folder holds the same two-line stub: it names the page by its folder and loads
../index.php, which renders that state. The sitemap is written by build/build_static.py from the
rendered pages. Run after any states.js change:

    python3 -I build/state_pages.py
"""
import json
import re
from pathlib import Path

SITE = Path(__file__).resolve().parent.parent / 'site'
STUB = "<?php\ndefine( 'VOTE_SLUG', basename( __DIR__ ) );\nrequire dirname( __DIR__ ) . '/index.php';\n"
# Spanish pages at /es/<slug>/ and /es/ (October 19): same template, VOTE_LANG set to es.
STUB_ES = "<?php\ndefine( 'VOTE_LANG', 'es' );\ndefine( 'VOTE_SLUG', basename( __DIR__ ) );\nrequire dirname( __DIR__, 2 ) . '/index.php';\n"
HUB_ES = "<?php\ndefine( 'VOTE_LANG', 'es' );\nrequire dirname( __DIR__ ) . '/index.php';\n"


def slug(name):
    return re.sub(r'[^a-z]+', '-', name.lower()).strip('-')


def main():
    rows = []
    for line in (SITE / 'states.js').read_text().splitlines():
        line = line.strip().rstrip(',')
        if line.startswith('{"abbr"'):
            rows.append(json.loads(line))
    assert len(rows) == 51, len(rows)
    slugs = {slug(r['name']) for r in rows}
    for d in SITE.iterdir():  # drop folders for states that no longer exist (none expected)
        if d.is_dir() and (d / 'index.php').exists() and d.name not in slugs and d.name != 'es':
            raise SystemExit(f'unexpected state folder {d.name}')
    for r in rows:
        d = SITE / slug(r['name'])
        d.mkdir(exist_ok=True)
        (d / 'index.php').write_text(STUB)
        e = SITE / 'es' / slug(r['name'])
        e.mkdir(parents=True, exist_ok=True)
        (e / 'index.php').write_text(STUB_ES)
    (SITE / 'es' / 'index.php').write_text(HUB_ES)
    print(f'{len(rows)} state folders in English and Spanish, plus the Spanish hub')


if __name__ == '__main__':
    main()
