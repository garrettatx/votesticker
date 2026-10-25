"""
SEO QA for votesticker.org (source: clients/hobby-sites/garrettn/docs/vote-cards/site/, built to dist/).

Fetches the hub and all 51 state pages and fails on: a non-200 page, missing or duplicate titles and
descriptions, more or fewer than one H1, a canonical that isn't the page's own clean URL, noindex on a
verified state (or its absence on an unchecked one), breadcrumb JSON-LD that doesn't end at the page,
any ?state= link, a link to garrettn.com, a broken internal link, a one-way neighbor link, a broken
previous/next chain, a sitemap that isn't exactly the indexable pages, and (unless --no-legacy) old
garrettn.com/vote/ URLs that don't 301 to the matching page on the new domain.

    python3 -I scripts/garrettn_vote_seo_qa.py [--base https://votesticker.org/] [--no-legacy]
Locally:  php -S 127.0.0.1:8791 -t site   or   python3 -m http.server 8792 -d dist   then --base http://127.0.0.1:879x/
"""
import argparse
import json
import re
import sys
import urllib.error
import urllib.request
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse

PROD = 'https://votesticker.org/'
LEGACY = 'https://www.garrettn.com/vote/'  # the first home (October 19, 2026); every URL there 301s to PROD


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *a, **k):
        return None


OPENER = urllib.request.build_opener(NoRedirect)


def fetch(url):
    req = urllib.request.Request(url, headers={'User-Agent': 'vote-seo-qa'})
    try:
        r = OPENER.open(req, timeout=30)
        return r.status, r.headers, r.read().decode('utf-8', 'replace')
    except urllib.error.HTTPError as e:
        return e.code, e.headers, e.read().decode('utf-8', 'replace')


class Page(HTMLParser):
    def __init__(self):
        super().__init__()
        self.title, self.meta, self.links, self.canon, self.h1, self.ld = '', {}, [], '', 0, []
        self.section, self.near, self.prev, self.next = None, {}, '', ''
        self._in, self._buf = None, ''

    def handle_starttag(self, tag, a):
        a = dict(a)
        if tag == 'title':
            self._in, self._buf = 'title', ''
        elif tag == 'meta' and (a.get('name') or a.get('property')):
            self.meta[a.get('name') or a.get('property')] = a.get('content', '')
        elif tag == 'link' and a.get('rel') == 'canonical':
            self.canon = a.get('href', '')
        elif tag == 'h1':
            self.h1 += 1
        elif tag == 'script' and a.get('type') == 'application/ld+json':
            self._in, self._buf = 'ld', ''
        elif tag == 'h2':
            self.section = a.get('id')
        elif tag == 'nav':
            self.section = None
        elif tag == 'a' and a.get('href'):
            self.links.append(a['href'])
            if a.get('rel') == 'prev':
                self.prev = a['href']
            if a.get('rel') == 'next':
                self.next = a['href']
            if self.section in ('near-h',):
                self.near.setdefault('near', []).append(a['href'])

    def handle_data(self, d):
        if self._in:
            self._buf += d

    def handle_endtag(self, tag):
        if tag == 'section':
            self.section = None
        if self._in == 'title' and tag == 'title':
            self.title, self._in = self._buf.strip(), None
        elif self._in == 'ld' and tag == 'script':
            self.ld.append(self._buf)
            self._in = None


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--base', default=PROD)
    ap.add_argument('--no-legacy', action='store_true', help='skip the garrettn.com/vote/ redirect checks')
    opts = ap.parse_args()
    base = opts.base
    base_path = urlparse(base).path
    fails = []
    fail = fails.append

    st, _, js = fetch(urljoin(base, 'states.js'))
    rows = [json.loads(l.strip().rstrip(',')) for l in js.splitlines() if l.strip().startswith('{"abbr"')]
    slug = lambda n: re.sub(r'[^a-z]+', '-', n.lower()).strip('-')
    paths = [''] + [slug(r['name']) + '/' for r in rows]
    verified = {slug(r['name']) + '/' for r in rows if r.get('status') == 'verified'}
    if len(rows) != 51:
        fail(f'states.js has {len(rows)} rows')

    pages, titles, descs = {}, {}, {}
    for p in paths + ['about/', 'history/']:  # pages, not states (no prev/next, hub share image)
        status, _, html = fetch(urljoin(base, p))
        if status != 200:
            fail(f'/{p}: HTTP {status}')
            continue
        pg = Page()
        pg.feed(html)
        pages[p] = pg
        if not pg.title or len(pg.title) > 65:
            fail(f'/{p}: title length {len(pg.title)}: {pg.title!r}')
        d = pg.meta.get('description', '')
        if not 70 <= len(d) <= 230:
            fail(f'/{p}: description length {len(d)}')
        if '—' in pg.title + d:
            fail(f'/{p}: em dash in title or description')
        titles.setdefault(pg.title, []).append(p)
        descs.setdefault(d, []).append(p)
        if pg.h1 != 1:
            fail(f'/{p}: {pg.h1} H1s')
        if pg.canon != PROD + p:
            fail(f'/{p}: canonical {pg.canon}')
        noindex = 'noindex' in pg.meta.get('robots', '')
        if p and noindex:
            fail(f'/{p}: page is noindex')  # every state page is indexable since October 19
        if not p and noindex:
            fail('hub is noindex')
        try:
            crumbs = json.loads(pg.ld[0])['itemListElement']
            if crumbs[-1]['item'] != PROD + p or len(crumbs) != (2 if p else 1) or crumbs[0]['item'] != PROD:
                fail(f'/{p}: breadcrumb ends at {crumbs[-1]["item"]}')
        except Exception as e:
            fail(f'/{p}: breadcrumb JSON-LD {e}')
        if '?state=' in html:
            fail(f'/{p}: has a ?state= link')
        og = pg.meta.get('og:image', '')
        # The card can come from the phase's set (index.php: og/ended/, og/post/, og-hub-post.jpg).
        want_og = (rf'{PROD}assets/og/((ended|post)/)?{re.escape(p[:-1])}\.jpg\?v=' if p and p not in ('about/', 'history/')
                   else rf'{PROD}assets/og-hub(-post)?\.jpg\?v=')
        if not re.match(want_og, og):
            fail(f'/{p}: og:image {og}')
        elif fetch(urljoin(base, og.replace(PROD, '')))[0] != 200:
            fail(f'/{p}: og:image does not load')
        # Every page but the home page has a copy-link button (removed from the home hero October 22).
        if p and ('id="share-link"' not in html or f'data-url="{PROD}{p}"' not in html):
            fail(f'/{p}: copy-link button missing or wrong url')
        old = [h for h in pg.links if 'garrettn.com' in h]
        if old:
            fail(f'/{p}: links to garrettn.com {old[:2]}')
        if 'href="https://www.garrettdigital.com/"' not in html or 'alt="Garrett Digital"' not in html:
            fail(f'/{p}: Garrett Digital logo link missing')
    for t, ps in titles.items():
        if len(ps) > 1:
            fail(f'duplicate title {t!r}: {ps}')
    for t, ps in descs.items():
        if len(ps) > 1:
            fail(f'duplicate description on {ps}')

    # Internal links: every same-site link on every page answers 200.
    internal = {urlparse(urljoin(base, h)).path for pg in pages.values() for h in pg.links if urljoin(base, h).startswith(base)}
    for path in sorted(internal):
        status, _, _ = fetch(urljoin(base, path))
        if status != 200:
            fail(f'link {path}: HTTP {status}')

    # Neighbors link both ways; previous/next walk all 51 states in order.
    rel = lambda h: urlparse(urljoin(base, h)).path[len(base_path):]
    for p, pg in pages.items():
        for h in pg.near.get('near', []):
            back = pages.get(rel(h))
            if back is not None and p not in [rel(x) for x in back.near.get('near', [])]:
                fail(f'/{p} lists /{rel(h)} as a neighbor, not the reverse')
    walk, cur = [], paths[1]
    while cur and cur not in walk:
        walk.append(cur)
        cur = rel(pages[cur].next) if pages.get(cur) and pages[cur].next else ''
    if walk != paths[1:]:
        fail(f'next chain covers {len(walk)} of 51')
    if pages.get(paths[1]) and pages[paths[1]].prev:
        fail('first state has a previous link')

    status, _, _ = fetch(urljoin(base, 'not-a-state/'))
    if status == 200:
        fail('/not-a-state/ answers 200')

    # The old home: hub, every state page, legacy ?state= links and the old sitemap all 301 to PROD.
    if not opts.no_legacy:
        # The old folder keeps only redirect scripts; its static files (old sitemap, assets) are gone and 404.
        for gone in ('sitemap.xml', 'app.js', 'not-a-state/'):
            status, _, _ = fetch(LEGACY + gone)
            if status != 404:
                fail(f'legacy {LEGACY}{gone}: {status}, want 404')
        checks = [('', ''), ('index.php', ''), ('?state=tx', 'texas/'), ('?state=dc', 'district-of-columbia/'),
                  ('?state=zz', ''), ('?state=%3Cx%3E', '')]
        checks += [(p, p) for p in paths[1:]]
        bad = 0
        for q, want in checks:
            status, h, _ = fetch(LEGACY + q)
            loc = h.get('Location', '')
            if status != 301 or loc != PROD + want:
                bad += 1
                if bad <= 5:
                    fail(f'legacy {LEGACY}{q}: {status} -> {loc}')
        if bad > 5:
            fail(f'legacy: {bad} of {len(checks)} old URLs do not redirect correctly')

    # Sitemap: exactly the hub and every state page (all indexable since October 19).
    status, _, xml = fetch(urljoin(base, 'sitemap.xml'))
    locs = {l for l in re.findall(r'<loc>(.*?)</loc>', xml) if '/es/' not in l}  # Spanish pages: scripts/qa_es.py
    want = {PROD} | {PROD + p for p in pages if p}
    if status != 200 or locs != want:
        fail(f'sitemap: HTTP {status}, extra {sorted(locs - want)[:3]}, missing {sorted(want - locs)[:3]}')

    print(f'{len(pages)} pages, {len(internal)} internal links, {len([p for p in pages if p and p not in ("about/", "history/")])} indexable states')
    for f in fails[:40]:
        print('  FAIL ' + f)
    print('FAILED (%d)' % len(fails) if fails else 'PASS')
    sys.exit(1 if fails else 0)


if __name__ == '__main__':
    main()
