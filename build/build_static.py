"""
Build the static site for Cloudflare Pages into dist/.

1. Copies every non-PHP file from site/ (scripts, styles, data, assets).
2. Renders the hub and the 51 state pages through build/render.php, which runs site/index.php exactly
   as the state folders' stubs do.
3. Writes sitemap.xml and robots.txt.

Sitemap rules (Google's guidance): list only canonical, indexable, 200 pages, by absolute URL; give
each an honest <lastmod>, the date its content last changed. changefreq and priority are left out
(Google ignores them). A page is indexable when its rendered HTML has no robots noindex, and its URL is
its own canonical. lastmod comes from build/lastmod.json: a hash of each page's rendered HTML (with
asset version stamps removed) and the date that hash last changed, so rebuilding without changes
keeps the old dates. Commit lastmod.json with the build.

    python3 -I build/build_static.py
    wrangler pages deploy dist --project-name votesticker --branch main
"""
import hashlib
import json
import re
import shutil
import subprocess
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SITE = ROOT / 'site'
DIST = ROOT / 'dist'
LASTMOD = ROOT / 'build' / 'lastmod.json'
SITE_URL = 'https://votesticker.org/'


def slug(name):
    return re.sub(r'[^a-z]+', '-', name.lower()).strip('-')


def render(slug_name=''):
    cmd = ['php', str(ROOT / 'build' / 'render.php')] + ([slug_name] if slug_name else [])
    out = subprocess.run(cmd, capture_output=True, text=True, check=True).stdout
    if '</html>' not in out or '<h1' not in out:  # the 404 render exits with status 0 too
        raise SystemExit(f'empty render for {slug_name or "hub"}: {out[:200]!r}')
    # PHP prints warnings into the page (October 26: 15,000 characters of them inside ID, ND, PA and VT
    # data-posts, live since Oct 21). Any of them stops the build.
    m = re.search(r'(?m)^(Warning|Deprecated|Notice|Fatal error|Parse error): .*', out)
    if m:
        raise SystemExit(f'PHP {m.group(1).lower()} in the render for {slug_name or "hub"}: {m.group(0)[:200]}')
    return out


def main():
    rows = [json.loads(l.strip().rstrip(',')) for l in (SITE / 'states.js').read_text().splitlines() if l.strip().startswith('{"abbr"')]
    assert len(rows) == 51, len(rows)
    if DIST.exists():
        shutil.rmtree(DIST)
    copied = 0
    for src in SITE.rglob('*'):
        if src.is_dir() or src.suffix == '.php' or src.name.startswith('.') or src.name == 'sitemap.xml':
            continue
        dst = DIST / src.relative_to(SITE)
        dst.parent.mkdir(parents=True, exist_ok=True)
        shutil.copy2(src, dst)
        copied += 1

    # English at the root, Spanish under es/ (same template, same states).
    pages = {'': render(), 'es/': render('es')}
    for r in rows:
        s = slug(r['name'])
        if not (SITE / s / 'index.php').exists() or not (SITE / 'es' / s / 'index.php').exists():
            raise SystemExit(f'missing state folder site/{s}/ or site/es/{s}/ (run build/state_pages.py)')
        pages[s + '/'] = render(s)
        pages['es/' + s + '/'] = render('es/' + s)
    pages['about/'] = render('about')
    pages['es/about/'] = render('es/about')
    hist = SITE / 'history-data.php'
    if not hist.exists() or '"draft":true' in hist.read_text():
        raise SystemExit('site/history-data.php is missing or a draft: run build/history_text.py once the facts are checked')
    pages['history/'] = render('history')
    pages['es/history/'] = render('es/history')
    for path, html in pages.items():
        d = DIST / path
        d.mkdir(parents=True, exist_ok=True)
        (d / 'index.html').write_text(html)

    # Sitemap: indexable canonical pages with the date their content last changed.
    today = date.today().isoformat()
    old = json.loads(LASTMOD.read_text()) if LASTMOD.exists() else {}
    new, urls = {}, []
    for path, html in pages.items():
        url = SITE_URL + path
        canon = re.search(r'<link rel="canonical" href="([^"]+)"', html)
        if re.search(r'<meta name="robots" content="[^"]*noindex', html) or not canon or canon.group(1) != url:
            continue
        # The countdown label ("12 days away") changes every day; it isn't a content change, so it
        # stays out of the hash along with the asset version stamps.
        h = hashlib.sha256(re.sub(r'<span data-countdown-label>[^<]*</span>', '', re.sub(r'\?v=\d+', '', html)).encode()).hexdigest()[:16]
        prev = old.get(path)
        new[path] = [h, prev[1] if prev and prev[0] == h else today]
        urls.append((url, new[path][1]))
    LASTMOD.write_text(json.dumps(new, indent=1, sort_keys=True) + '\n')
    # Each URL lists its language pair (Google's sitemap form of hreflang) when both pages are indexable.
    indexable = {u for u, _ in urls}
    def alts(u):
        tail = u[len(SITE_URL):]
        en = SITE_URL + (tail[3:] if tail.startswith('es/') else tail)
        es = SITE_URL + 'es/' + (tail[3:] if tail.startswith('es/') else tail)
        if en not in indexable or es not in indexable:
            return ''
        return (f'\n    <xhtml:link rel="alternate" hreflang="en" href="{en}"/>'
                f'\n    <xhtml:link rel="alternate" hreflang="es" href="{es}"/>'
                f'\n    <xhtml:link rel="alternate" hreflang="x-default" href="{en}"/>')
    # Each page also lists the sticker images it shows (Google's image sitemap), by the exact src on the page.
    def imgs(u):
        html = pages[u[len(SITE_URL):]]
        srcs = dict.fromkeys(s for s in re.findall(r'<img[^>]+src="(/assets/[^"]+)"', html) if 'logo' not in s)
        return ''.join(f'\n    <image:image><image:loc>{SITE_URL}{s[1:].replace("&", "&amp;")}</image:loc></image:image>' for s in srcs)
    xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">']
    xml += [f'  <url>\n    <loc>{u}</loc>\n    <lastmod>{m}</lastmod>{alts(u)}{imgs(u)}\n  </url>' for u, m in urls]
    xml.append('</urlset>')
    (DIST / 'sitemap.xml').write_text('\n'.join(xml) + '\n')
    (DIST / '404.html').write_text(render('404'))
    # Calendar files (final-week loop item 1): one per state and language, for the build's date. The
    # Nov 4 post-election build writes none, and the state panel drops the link with them.
    cals = 0
    for p in ('', 'es/'):
        for r in rows:
            ics = subprocess.run(['php', str(ROOT / 'build' / 'render.php'), f'{p}cal/{slug(r["name"])}'], capture_output=True, check=True).stdout.decode()  # bytes: text mode would turn CRLF into LF
            if re.search(r'(?m)^(Warning|Deprecated|Notice|Fatal error|Parse error): ', ics):
                raise SystemExit(f'PHP warning in the calendar for {p}{r["name"]}')
            if 'BEGIN:VEVENT' not in ics:
                continue
            if not ics.startswith('BEGIN:VCALENDAR\r\n') or not ics.endswith('END:VCALENDAR\r\n') or any(len(l.encode()) > 75 for l in ics.split('\r\n')):
                raise SystemExit(f'bad calendar file for {p}{r["name"]}')
            d = DIST / p / 'cal'
            d.mkdir(parents=True, exist_ok=True)
            (d / f'{slug(r["name"])}.ics').write_bytes(ics.encode())
            cals += 1
    (DIST / 'es' / '404.html').write_text(render('es/404'))  # Pages serves the nearest 404.html
    (DIST / 'robots.txt').write_text(f'User-agent: *\nAllow: /\n\nSitemap: {SITE_URL}sitemap.xml\n')
    # Share loop (October 23): the link in the suggested post text is /go/<state>/<version>, which lands
    # on the state page tagged as a friend arriving from someone's post. 302 so the tags can change later.
    # One rule per version: production Pages leaves a placeholder in the query string unfilled
    # (utm_content=%3Aversion live on October 24), though the path placeholder works.
    utm = 'utm_source=sticker_post&utm_medium=social&utm_campaign=vote_2026'
    (DIST / '_redirects').write_text(''.join(
        ''.join(f'{p}go/:state/{v} /{p[1:]}:state/?{utm}&utm_content={v} 302\n' for v in ('plan', 'short', 'friends', 'official'))
        + f'{p}go/:state/* /{p[1:]}:state/?{utm} 302\n{p}go/:state /{p[1:]}:state/?{utm} 302\n'
        for p in ('/es/', '/')))
    print(f'dist/: {copied} files copied, {len(pages)} pages rendered, sitemap {len(urls)} URLs, {cals} calendar files')


if __name__ == '__main__':
    main()
