# External link check for the state pages (EN and ES): python3 -I scripts/qa_links.py dist
# Prints every URL that did not return 200. Government sites often answer 403 or fail TLS to scripts;
# treat those as blocks, not dead links, and confirm a 404 or DNS error with curl or Chrome first.
import re, glob, sys, collections, concurrent.futures, urllib.request, ssl
root = sys.argv[1]; urls = collections.defaultdict(set)
for f in glob.glob(root + '/*/index.html') + glob.glob(root + '/es/*/index.html'):
    for u in re.findall(r'href="(https?://[^"#]+)', open(f).read()):
        if 'votesticker.org' in u or 'garrettn' in u: continue
        urls[u.replace('&amp;', '&')].add(f.split(root + '/')[1].rsplit('/index.html')[0])
UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36'
def get(u):
    try:
        r = urllib.request.urlopen(urllib.request.Request(u, headers={'User-Agent': UA, 'Accept': 'text/html,*/*'}), timeout=25)
        return u, r.status, r.geturl()
    except urllib.error.HTTPError as e: return u, e.code, ''
    except Exception as e: return u, 'ERR ' + type(e).__name__ + ' ' + str(e)[:60], ''
with concurrent.futures.ThreadPoolExecutor(12) as x:
    res = list(x.map(get, sorted(urls)))
print(len(res), 'urls')
for u, s, final in res:
    if s != 200: print(s, u, sorted(urls[u])[:4])
