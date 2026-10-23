#!/usr/bin/env python3
"""Archive VoteSticker search and analytics data to BigQuery (October 23, 2026).

Search Console keeps 16 months and GA4 explorations 2 to 14, so this election's data would be gone
before 2028. Everything lands in dataset gsc_votesticker (project from the shared service account):

  gsc_page_query   day x page x query x country x device   (only queries Google shows, about 20%)
  gsc_page         day x page                             (every impression, hidden queries included; adding
                   country or device drops the hidden-query rows, 844 to 167 on Oct 21, so it has neither)
  ga4_landing      day x landing page x channel x source/medium/campaign/content
  ga4_events       day x event x page path (custom parameters once they are registered in GA4)
  index_status     one row per URL per --index run (URL Inspection)
  state_calendar   each state's voting window from site/states.js (replaced every run)
  change_log       docs/change-log.csv (replaced every run)

Each day is written to its own partition with WRITE_TRUNCATE, so re-running a day replaces it rather
than doubling it. Search Console data settles over about three days, so each run re-pulls the last
--days days (default 14, enough overlap for a weekly run).

  python3 scripts/archive_search.py                    # last 14 days, plus the two reference tables
  python3 scripts/archive_search.py --since 2026-09-01 # backfill
  python3 scripts/archive_search.py --index            # also inspect all sitemap URLs (~10 minutes)
  python3 scripts/archive_search.py --dry-run          # fetch and count, write nothing
"""
import argparse
import csv
import json
import subprocess
import sys
import tempfile
from datetime import date, timedelta
from pathlib import Path

from google.oauth2 import service_account
from googleapiclient.discovery import build
from google.cloud import bigquery

HERE = Path(__file__).resolve().parents[1]           # the votesticker repo
ROOT = HERE.parents[2]                               # seo-command-center, which holds config.json
PROPERTY = 'sc-domain:votesticker.org'
GA4 = 'properties/559738937'
DATASET = 'gsc_votesticker'

S, I, F, D = 'STRING', 'INTEGER', 'FLOAT', 'DATE'
SCHEMAS = {
    'gsc_page_query': [('data_date', D), ('page', S), ('query', S), ('country', S), ('device', S), ('clicks', I), ('impressions', I), ('position', F)],
    'gsc_page': [('data_date', D), ('page', S), ('clicks', I), ('impressions', I), ('position', F)],
    'ga4_landing': [('data_date', D), ('landing_page', S), ('channel', S), ('source', S), ('medium', S), ('campaign', S), ('content', S), ('sessions', I), ('engaged_sessions', I), ('new_users', I)],
    'ga4_events': [('data_date', D), ('event_name', S), ('page_path', S), ('event_count', I)],
    'index_status': [('data_date', D), ('url', S), ('coverage', S), ('last_crawl', S), ('google_canonical', S), ('page_fetch', S)],
    'state_calendar': [('abbr', S), ('name', S), ('status', S), ('kind', S), ('early_start', D), ('early_end', D), ('varies', S), ('election_day', D), ('checked', D), ('source_url', S)],
    'change_log': [('data_date', D), ('area', S), ('change', S), ('commit', S)],
}
PARTITIONED = {'gsc_page_query', 'gsc_page', 'ga4_landing', 'ga4_events', 'index_status', 'change_log'}


def clients():
    cfg = json.load(open(ROOT / 'config.json'))
    key = str(ROOT / cfg['service_account_key'])
    creds = service_account.Credentials.from_service_account_file(key, scopes=[
        'https://www.googleapis.com/auth/webmasters.readonly', 'https://www.googleapis.com/auth/analytics.readonly',
        'https://www.googleapis.com/auth/bigquery'])
    from google.analytics.data_v1beta import BetaAnalyticsDataClient
    project = json.load(open(key))['project_id']
    return (build('searchconsole', 'v1', credentials=creds, cache_discovery=False),
            BetaAnalyticsDataClient(credentials=creds), bigquery.Client(credentials=creds, project=project), project)


def ensure(bq, project):
    ds = bigquery.Dataset(f'{project}.{DATASET}')
    ds.location = 'US'
    ds.description = 'VoteSticker.org Search Console, GA4 and context tables (scripts/archive_search.py)'
    ds.default_table_expiration_ms = None    # the project default is 60 days; this archive is permanent
    ds.default_partition_expiration_ms = None
    bq.create_dataset(ds, exists_ok=True)
    for name, cols in SCHEMAS.items():
        t = bigquery.Table(f'{project}.{DATASET}.{name}', schema=[bigquery.SchemaField(c, k) for c, k in cols])
        if name in PARTITIONED:
            t.time_partitioning = bigquery.TimePartitioning(field='data_date', type_=bigquery.TimePartitioningType.DAY)
        bq.create_table(t, exists_ok=True)


def write(bq, project, name, rows, day=None, dry=False):
    """Replace one day's partition (or the whole table when day is None) with rows."""
    target = f'{project}.{DATASET}.{name}' + (f'${day.replace("-", "")}' if day else '')
    if dry:
        print(f'  would write {len(rows):>6} rows to {target}')
        return
    job = bigquery.LoadJobConfig(schema=[bigquery.SchemaField(c, k) for c, k in SCHEMAS[name]],
                                 write_disposition=bigquery.WriteDisposition.WRITE_TRUNCATE,
                                 source_format=bigquery.SourceFormat.NEWLINE_DELIMITED_JSON)
    bq.load_table_from_json(rows, target, job_config=job).result()
    print(f'  wrote {len(rows):>6} rows to {target}')


def gsc_rows(gsc, day, dims):
    rows, start = [], 0
    while True:
        got = gsc.searchanalytics().query(siteUrl=PROPERTY, body={
            'startDate': day, 'endDate': day, 'dimensions': dims, 'rowLimit': 25000, 'startRow': start,
            'dataState': 'all'}).execute().get('rows', [])
        rows += got
        start += len(got)
        if len(got) < 25000:
            return rows


def ga4_rows(ga, day, dims, metrics):
    from google.analytics.data_v1beta.types import RunReportRequest, DateRange, Dimension, Metric
    out, offset = [], 0
    while True:
        r = ga.run_report(RunReportRequest(property=GA4, date_ranges=[DateRange(start_date=day, end_date=day)],
                                           dimensions=[Dimension(name=d) for d in dims], metrics=[Metric(name=m) for m in metrics],
                                           limit=100000, offset=offset))
        out += [[v.value for v in x.dimension_values] + [int(v.value) for v in x.metric_values] for x in r.rows]
        offset += len(r.rows)
        if offset >= r.row_count or not r.rows:
            return out


def archive_day(gsc, ga, bq, project, day, dry):
    print(day)
    pq = gsc_rows(gsc, day, ['page', 'query', 'country', 'device'])
    write(bq, project, 'gsc_page_query', [dict(zip(['page', 'query', 'country', 'device'], r['keys']), data_date=day, clicks=int(r['clicks']),
                                                impressions=int(r['impressions']), position=r['position']) for r in pq], day, dry)
    pg = gsc_rows(gsc, day, ['page'])
    write(bq, project, 'gsc_page', [dict(page=r['keys'][0], data_date=day, clicks=int(r['clicks']),
                                         impressions=int(r['impressions']), position=r['position']) for r in pg], day, dry)
    lp = ga4_rows(ga, day, ['landingPage', 'sessionDefaultChannelGroup', 'sessionSource', 'sessionMedium', 'sessionCampaignName', 'sessionManualAdContent'],
                  ['sessions', 'engagedSessions', 'newUsers'])
    write(bq, project, 'ga4_landing', [dict(zip(['landing_page', 'channel', 'source', 'medium', 'campaign', 'content', 'sessions', 'engaged_sessions', 'new_users'], r),
                                            data_date=day) for r in lp], day, dry)
    ev = ga4_rows(ga, day, ['eventName', 'pagePath'], ['eventCount'])
    write(bq, project, 'ga4_events', [dict(zip(['event_name', 'page_path', 'event_count'], r), data_date=day) for r in ev], day, dry)


def state_calendar():
    election = '2026-11-03'
    rows = [json.loads(l.strip().rstrip(',')) for l in (HERE / 'site' / 'states.js').read_text().splitlines() if l.strip().startswith('{"abbr"')]
    return [{'abbr': r['abbr'], 'name': r['name'], 'status': r.get('status'), 'kind': r.get('kind'),
             'early_start': r.get('start') or None, 'early_end': r.get('end') or None, 'varies': str(r.get('varies', '')).lower() or None,
             'election_day': election, 'checked': (r.get('checked') or '')[:10] or None, 'source_url': r.get('sourceUrl')} for r in rows]


def change_log():
    return [{'data_date': r['date'], 'area': r['area'], 'change': r['change'], 'commit': r['commit'] or None}
            for r in csv.DictReader(open(HERE / 'docs' / 'change-log.csv'))]


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--since', help='first day to archive, YYYY-MM-DD')
    ap.add_argument('--days', type=int, default=14, help='days back from yesterday when --since is not given')
    ap.add_argument('--index', action='store_true', help='also run URL Inspection on every sitemap URL')
    ap.add_argument('--dry-run', action='store_true')
    a = ap.parse_args()
    gsc, ga, bq, project = clients()
    if not a.dry_run:
        ensure(bq, project)
    end = date.today() - timedelta(days=1)
    day = date.fromisoformat(a.since) if a.since else end - timedelta(days=a.days - 1)
    while day <= end:
        archive_day(gsc, ga, bq, project, day.isoformat(), a.dry_run)
        day += timedelta(days=1)
    write(bq, project, 'state_calendar', state_calendar(), None, a.dry_run)
    # The change log is small and hand-kept, so it is rewritten whole (the partitions follow its dates).
    write(bq, project, 'change_log', change_log(), None, a.dry_run)
    if a.index:
        with tempfile.NamedTemporaryFile(suffix='.json') as tmp:
            subprocess.run([sys.executable, '-u', str(HERE / 'scripts' / 'gsc_inspect.py'), tmp.name], check=True)
            today = date.today().isoformat()
            rows = [{'data_date': today, 'url': o['url'], 'coverage': o.get('coverage') or o.get('error'), 'last_crawl': o.get('lastCrawl'),
                     'google_canonical': o.get('googleCanonical'), 'page_fetch': o.get('fetch')} for o in json.load(open(tmp.name))]
            write(bq, project, 'index_status', rows, today, a.dry_run)


if __name__ == '__main__':
    main()
