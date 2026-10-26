"""
Tell IndexNow (Bing, Yandex, Seznam, Naver and others) which pages changed, after a deploy.

Reads the live sitemap, compares each URL's <lastmod> with what was last submitted
(build/indexnow-sent.json), and submits only the changed URLs. The key file lives at
site/<key>.txt and deploys to https://votesticker.org/<key>.txt, which is how IndexNow checks
that the request comes from the site owner.

    python3 -I scripts/indexnow.py            # submit what changed since the last run
    python3 -I scripts/indexnow.py --all      # submit every sitemap URL
    python3 -I scripts/indexnow.py --dry-run  # list what would be sent
"""
import argparse
import json
import re
import sys
import urllib.error
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SITE = 'https://votesticker.org/'
KEY = 'f691b1b3d7674b378f82c72b1b27f5d8'
STATE = ROOT / 'build' / 'indexnow-sent.json'
ENDPOINT = 'https://api.indexnow.org/indexnow'


def get(url):
    req = urllib.request.Request(url, headers={'User-Agent': 'votesticker-indexnow'})
    with urllib.request.urlopen(req, timeout=30) as r:
        return r.read().decode('utf-8')


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--all', action='store_true')
    ap.add_argument('--dry-run', action='store_true')
    a = ap.parse_args()
    if get(SITE + KEY + '.txt').strip() != KEY:
        sys.exit(f'key file at {SITE}{KEY}.txt is missing or wrong; deploy first')
    xml = get(SITE + 'sitemap.xml')
    live = dict(re.findall(r'<url>\s*<loc>([^<]+)</loc>\s*<lastmod>([^<]+)</lastmod>', xml))
    if not live:
        sys.exit('no URLs with lastmod found in the sitemap')
    sent = json.loads(STATE.read_text()) if STATE.exists() else {}
    # Compare each page's content fingerprint from the build (build/lastmod.json), not the sitemap date:
    # a date only changes once a day, so a second deploy the same day sent nothing (October 26). Pages the
    # build record doesn't know fall back to the date.
    lm = json.loads((ROOT / 'build' / 'lastmod.json').read_text()) if (ROOT / 'build' / 'lastmod.json').exists() else {}
    mark = {u: (lm.get(u[len(SITE):]) or [m])[0] for u, m in live.items()}
    todo = sorted(live) if a.all else sorted(u for u in live if sent.get(u) != mark[u])
    print(f'{len(live)} sitemap URLs, {len(todo)} to submit')
    if not todo or a.dry_run:
        for u in todo[:20]:
            print('  ' + u)
        return
    body = json.dumps({'host': 'votesticker.org', 'key': KEY, 'keyLocation': SITE + KEY + '.txt', 'urlList': todo}).encode()
    req = urllib.request.Request(ENDPOINT, data=body, headers={'Content-Type': 'application/json; charset=utf-8'})
    try:
        with urllib.request.urlopen(req, timeout=30) as r:
            code = r.status
    except urllib.error.HTTPError as e:
        sys.exit(f'IndexNow answered HTTP {e.code}: {e.read().decode("utf-8", "replace")[:300]}')
    print(f'IndexNow answered HTTP {code}')  # 200 = accepted, 202 = accepted, key check pending
    if code in (200, 202):
        sent.update({u: mark[u] for u in todo})
        STATE.write_text(json.dumps(sent, indent=1, sort_keys=True) + '\n')


if __name__ == '__main__':
    main()
