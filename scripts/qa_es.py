"""
Spanish QA for votesticker.org/es/: checks every English page against its Spanish pair.

Fails on: a non-200 page; lang not "es"; a canonical that isn't the page's own /es/ URL; hreflang that
isn't reciprocal (or present while Spanish is still noindex); a language switch that doesn't point at
the exact pair, both ways; a page link on a Spanish page that leaves /es/; English left on a Spanish
page (sentinel phrases and English function words, after removing allowed proper nouns); a share image
or og:locale that isn't Spanish; and structural parity: the same number of FAQs, related-state links,
pager links, and state links in both languages (es-mx-localization: a dropped component is
release-blocking). Also checks that Spanish pages stay out of the sitemap while noindex.

    python3 -I scripts/qa_es.py [--base https://votesticker.org/]
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

# English that must never appear in a Spanish page's visible text.
SENTINELS = ['Vote Stickers', 'Early voting', 'early voting', 'Ready-Made', 'Make your', 'Make Your', 'Copy link', 'Official info',
             'Before You Vote', 'Common Questions', 'Download picture', 'Previous state', 'Next state', 'States Next Door',
             'Election Day', 'Find your', 'Pick a', 'This tool', 'built by', 'Your sticker', 'Tap one', 'votes by mail',
             'Browse All', 'All States', 'Looking for', 'Type a state', 'Suggested post', 'Preview circle', 'Main message']
# Proper nouns and bracketed official titles that stay in English on purpose.
ALLOWED = ['Vote Sticker', 'VoteSticker.org', 'League of Women Voters', 'Supervisor of Elections', 'county board of elections',
           'local election authority', 'county auditor', 'Secretary of State', 'Garrett Digital', 'English']
FUNCTION_WORDS = re.compile(r"\b(the|and|your|with|from|for|this|that|you|are|vote early|by mail)\b", re.I)


def fetch(url):
    req = urllib.request.Request(url, headers={'User-Agent': 'vote-es-qa'})
    try:
        r = urllib.request.urlopen(req, timeout=30)
        return r.status, r.read().decode('utf-8', 'replace')
    except urllib.error.HTTPError as e:
        return e.code, e.read().decode('utf-8', 'replace')


class Page(HTMLParser):
    def __init__(self):
        super().__init__()
        self.lang = ''
        self.canon = ''
        self.alts = {}
        self.meta = {}
        self.links = []
        self.switch = ''
        self.text = []
        self.ld = []
        self.counts = {'faq': 0, 'near': 0, 'pager': 0, 'states': 0}
        self._skip = 0
        self._in_ld = False
        self._zone = []

    def handle_starttag(self, tag, a):
        a = dict(a)
        cls = a.get('class') or ''
        if tag == 'html':
            self.lang = a.get('lang', '')
        if tag in ('script', 'style'):
            self._skip += 1
            self._in_ld = a.get('type') == 'application/ld+json'
            if self._in_ld:
                self.ld.append('')
        if tag == 'link' and a.get('rel') == 'canonical':
            self.canon = a.get('href', '')
        if tag == 'link' and a.get('rel') == 'alternate' and a.get('hreflang'):
            self.alts[a['hreflang']] = a.get('href', '')
        if tag == 'meta' and (a.get('name') or a.get('property')):
            self.meta[a.get('name') or a.get('property')] = a.get('content', '')
        if tag in ('section', 'nav', 'div') and any(c in cls.split() for c in ('faq', 'near', 'pager', 'states')):
            self._zone.append(next(c for c in ('faq', 'near', 'pager', 'states') if c in cls.split()))
        elif tag in ('section', 'nav', 'div'):
            self._zone.append('')
        if tag == 'details' and 'faq' in self._zone:
            self.counts['faq'] += 1
        if tag == 'a' and a.get('href'):
            self.links.append(a['href'])
            if 'lang-switch' in cls:
                self.switch = a['href']
            for z in ('near', 'pager', 'states'):
                if z in self._zone:
                    self.counts[z] += 1
        if tag == 'img' and a.get('alt'):
            self.text.append(a['alt'])
        for attr in ('aria-label', 'placeholder'):
            if a.get(attr):
                self.text.append(a[attr])

    def handle_endtag(self, tag):
        if tag in ('script', 'style'):
            self._skip -= 1
            self._in_ld = False
        if tag in ('section', 'nav', 'div') and self._zone:
            self._zone.pop()

    def handle_data(self, d):
        if self._in_ld:
            self.ld[-1] += d
        elif not self._skip:
            self.text.append(d)


def parse(html):
    pg = Page()
    pg.feed(html)
    return pg


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--base', default=PROD)
    base = ap.parse_args().base
    fails = []
    fail = fails.append
    _, js = fetch(urljoin(base, 'states.js'))
    rows = [json.loads(l.strip().rstrip(',')) for l in js.splitlines() if l.strip().startswith('{"abbr"')]
    slug = lambda n: re.sub(r'[^a-z]+', '-', n.lower()).strip('-')
    paths = [''] + [slug(r['name']) + '/' for r in rows] + ['about/']
    gated = None
    links = set()
    for p in paths:
        s_en, en_html = fetch(urljoin(base, p))
        s_es, es_html = fetch(urljoin(base, 'es/' + p))
        if s_en != 200 or s_es != 200:
            fail(f'/{p}: HTTP en {s_en}, es {s_es}')
            continue
        en, es = parse(en_html), parse(es_html)
        noindex = 'noindex' in es.meta.get('robots', '')
        en_noindex = 'noindex' in en.meta.get('robots', '')
        gated = noindex if gated is None else gated  # the hub decides: Spanish still under review or live
        if es.lang != 'es' or en.lang != 'en':
            fail(f'/{p}: lang en={en.lang!r} es={es.lang!r}')
        if es.canon != PROD + 'es/' + p:
            fail(f'/es/{p}: canonical {es.canon}')
        want = {'en': PROD + p, 'es': PROD + 'es/' + p, 'x-default': PROD + p}
        if gated:
            if es.alts or en.alts:
                fail(f'/{p}: hreflang present while Spanish is under review')
        else:
            if es.alts != want or en.alts != want:
                fail(f'/{p}: hreflang en={en.alts} es={es.alts}')
            if noindex != en_noindex:
                fail(f'/{p}: noindex en={en_noindex} es={noindex}')
        if urljoin(base, es.switch) not in (PROD + p, urljoin(base, p)) or urljoin(base, en.switch) not in (PROD + 'es/' + p, urljoin(base, 'es/' + p)):
            fail(f'/{p}: language switch en->{en.switch} es->{es.switch}')
        if es.meta.get('og:locale') != 'es_MX' or not re.search(r'/assets/(og/es/|og-hub-es)', es.meta.get('og:image', '')):
            fail(f'/es/{p}: og:locale {es.meta.get("og:locale")} og:image {es.meta.get("og:image")}')
        try:
            crumbs = json.loads(es.ld[0])['itemListElement']
            if crumbs[0]['item'] != PROD + 'es/':
                fail(f'/es/{p}: breadcrumb starts at {crumbs[0]["item"]}')
        except Exception as e:
            fail(f'/es/{p}: breadcrumb JSON-LD {e}')
        for h in es.links:
            u = urlparse(urljoin(base, h))
            if h.startswith('#') or u.netloc != urlparse(base).netloc or h == es.switch or u.path.startswith('/assets/'):
                continue
            if not u.path.startswith('/es/'):
                fail(f'/es/{p}: link leaves Spanish: {h}')
            links.add(u.path)
        text = ' '.join(' '.join(es.text).split())
        for a in ALLOWED:
            text = text.replace(a, '')
        left = [s for s in SENTINELS if s in text] + sorted(set(m.group(0) for m in FUNCTION_WORDS.finditer(text)))
        if left:
            fail(f'/es/{p}: English left: {left[:6]}')
        if en.counts != es.counts:
            fail(f'/{p}: structure en {en.counts} vs es {es.counts}')
    for path in sorted(links):
        if fetch(urljoin(base, path))[0] != 200:
            fail(f'link {path} does not load')
    _, xml = fetch(urljoin(base, 'sitemap.xml'))
    es_in_sitemap = '/es/' in xml
    if gated and es_in_sitemap:
        fail('sitemap lists Spanish pages while they are noindex')
    if gated is False and not es_in_sitemap:
        fail('Spanish pages are indexable but missing from the sitemap')
    print(f'{len(paths)} page pairs, {len(links)} Spanish internal links, Spanish {"noindex (review pending)" if gated else "indexable"}')
    for f in fails[:40]:
        print('  FAIL ' + f)
    print(f'FAILED ({len(fails)})' if fails else 'PASS')
    sys.exit(1 if fails else 0)


if __name__ == '__main__':
    main()
