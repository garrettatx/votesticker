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
        h = hashlib.sha256(re.sub(r'\?v=\d+', '', html).encode()).hexdigest()[:16]
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
    xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">']
    xml += [f'  <url>\n    <loc>{u}</loc>\n    <lastmod>{m}</lastmod>{alts(u)}\n  </url>' for u, m in urls]
    xml.append('</urlset>')
    (DIST / 'sitemap.xml').write_text('\n'.join(xml) + '\n')
    (DIST / '404.html').write_text(render('404'))
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
    print(f'dist/: {copied} files copied, {len(pages)} pages rendered, sitemap {len(urls)} URLs')


if __name__ == '__main__':
    main()
