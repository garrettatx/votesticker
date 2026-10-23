# Analytics: What VoteSticker Logs

Set up October 23, 2026. GA4 property 559738937, web stream 16162444389, measurement ID G-PZWZTXDSDE.
Search Console `sc-domain:votesticker.org`. Both are read by the shared service account
(`config.json` `service_account_key` in seo-command-center).

## Rules

- **One event per thing someone finishes.** Saving a sticker, copying the post text, sharing the page
  link, a search that found no state. Never per click on a design tile, color swatch or chip, and no
  scroll or hover events. Enhanced measurement (page views, scrolls, outbound clicks) stays as GA4
  ships it.
- **Parameter values are short preset ids**, never free text: state `TX`, design `tilt`, message `early`.
  The one exception is `search_term`, which is folded to lowercase letters and cut to 30 characters.
- **snake_case names**, under GA4's limits (40 characters for names, 100 for values). `share` is GA4's
  recommended event and uses its recommended parameters; the rest are custom because no recommended
  event fits.
- **No analytics off the live site.** `site/index.php` loads Google and Ahrefs analytics only when the
  hostname is `votesticker.org` and `navigator.webdriver` is false, so local previews and every
  Playwright QA run (which sets webdriver) send nothing. Before October 24 the live QA runs counted as
  Direct visits.
- **QA:** `node scripts/qa_events.mjs` fails on a missing or wrong parameter, an event not listed here,
  more than one event per action, or analytics loading in an automated browser. Add an event here and
  in `ALLOWED` in that script together.

## Events

| Event | When | Parameters |
|---|---|---|
| `sticker_save` | A sticker was saved: download started (Android, desktop), share sheet completed or press-and-hold shown (iPhone) | `state`, `sticker_source` (`ready_made` or `maker`), `design` (maker design id, or `ready_1` to `ready_10` for the set), `palette` and `message` (maker only), `save_method` (`download`, `share_sheet`, `press_hold`) |
| `sticker_save_cancel` | iPhone share sheet closed without saving | same as `sticker_save`, `save_method` `cancelled` |
| `post_text_copy` | Copy post text | `state`, `message`, `post_variant` (`plan`, `short`, `friends`, `official`), `post_edited` (`yes`, `no`) |
| `share` | Page link shared or copied | `method` (`share_sheet`, `copy_link`), `content_type` `page`, `item_id` (state or `hub`) |
| `state_search_miss` | The state finder matched nothing (on Enter, Go or leaving the box; once per term per page) | `search_term` |

Message ids are per language: English `early`, `nov3`, `ivoted`, `voted`, `mail`, `mailed`, `ballotin`;
Spanish `votatemprano`, `vota3nov`, `voyavotar`, `yavote`, `yavotetemprano`, `votacorreo`,
`yavotecorreo`. The page path tells the language.

`press_hold` counts the picture being shown full screen. iOS doesn't report whether the person then
saved it, so it is the closest available signal.

## The Share Loop

The link in every suggested post is `https://votesticker.org/go/<state>/<version>` (Spanish
`/es/go/...`). `dist/_redirects`, written by `build/build_static.py`, sends it with a 302 to the state
page tagged:

`?utm_source=sticker_post&utm_medium=social&utm_campaign=vote_2026&utm_content=<version>`

So a friend who taps a link in someone's post shows in GA4 as source `sticker_post` (channel Organic
Social), and `utm_content` says which post version brought them. The page's canonical tag has no
query string, so the tags don't create duplicate URLs for Google. Posts copied before October 24 link
to `/texas` without tags; those visits stay in Direct or Referral.

## The BigQuery Archive

`python3 scripts/archive_search.py` writes dataset `gsc_votesticker` in project `seo-command-ctr`; the
table list is in the script's docstring. Each day is its own partition, replaced on re-run. The
seo-command-center weekly job (`scripts/run_weekly.py`, Sundays 3 p.m.) runs it for the last 14 days.
Run `--index` by hand for an index-status snapshot (about 10 minutes for 108 URLs).

Search Console's API shows queries for about 20% of impressions; the rest are hidden as rare queries.
`gsc_page` (day x page, no other dimension) keeps every impression. Adding country or device to a page
query drops the hidden rows (844 impressions to 167 on October 21), so do that only in `gsc_page_query`.
The native bulk export (backlog) is the only way to get country and device for the hidden rows.

Context tables: `state_calendar` (from `site/states.js`) and `change_log` (from `docs/change-log.csv`,
one row per change that could move traffic; add a row when something ships).

## Not Done Yet (Garrett, in GA4 and Search Console)

In `docs/backlog.md` under Analytics: register the custom dimensions, mark `sticker_save` as a key
event, set data retention to 14 months, link GA4 to BigQuery, and turn on Search Console bulk export.
