# URL Inspection for every sitemap URL on sc-domain:votesticker.org, with the shared service account.
#   python3 scripts/gsc_inspect.py [out.json]
import json, re, sys, time, urllib.request
from pathlib import Path
from google.oauth2 import service_account
from googleapiclient.discovery import build
ROOT = Path(__file__).resolve().parents[4]  # seo-command-center, which holds config.json
cfg = json.load(open(ROOT / 'config.json'))
creds = service_account.Credentials.from_service_account_file(str(ROOT / cfg['service_account_key']),
    scopes=['https://www.googleapis.com/auth/webmasters'])
gsc = build('searchconsole', 'v1', credentials=creds, cache_discovery=False)
sites = [s['siteUrl'] for s in gsc.sites().list().execute().get('siteEntry', []) if 'votesticker' in s['siteUrl']]
print('PROPERTIES', sites)
if not sites: sys.exit(1)
prop = sites[0]
for sm in gsc.sitemaps().list(siteUrl=prop).execute().get('sitemap', []):
    print('SITEMAP', sm.get('path'), 'submitted', sm.get('lastSubmitted'), 'downloaded', sm.get('lastDownloaded'), 'errors', sm.get('errors'), 'warnings', sm.get('warnings'))
xml = urllib.request.urlopen(urllib.request.Request('https://votesticker.org/sitemap.xml', headers={'User-Agent': 'Mozilla/5.0'})).read().decode()
urls = re.findall(r'<loc>([^<]+)</loc>', xml)
print('URLS', len(urls))
out = []
for u in urls:
    for attempt in range(3):
        try:
            r = gsc.urlInspection().index().inspect(body={'inspectionUrl': u, 'siteUrl': prop}).execute()
            break
        except Exception as e:
            err = str(e); time.sleep(3)
    else:
        out.append({'url': u, 'error': err}); continue
    ix = r['inspectionResult'].get('indexStatusResult', {})
    out.append({'url': u, 'verdict': ix.get('verdict'), 'coverage': ix.get('coverageState'),
                'lastCrawl': ix.get('lastCrawlTime'), 'googleCanonical': ix.get('googleCanonical'),
                'userCanonical': ix.get('userCanonical'), 'robots': ix.get('robotsTxtState'), 'fetch': ix.get('pageFetchState')})
if len(sys.argv) > 1: json.dump(out, open(sys.argv[1], 'w'), indent=1)
for o in out:
    if o.get('coverage') != 'Submitted and indexed': print('NOT INDEXED', o['url'], o.get('coverage') or o.get('error'))
from collections import Counter
print(Counter(o.get('coverage', 'ERROR') for o in out))
