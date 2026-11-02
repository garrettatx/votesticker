# Sticker Library, 2026 Archive and Next-Cycle Plan

Status: plan, not approved. Written November 2, 2026 from Garrett's requests that day (one page that shows
every sticker, linked from the footer and the galleries; a way to archive 2026; a setup that spins up the next
election) and four pasted outside reads on image SEO, URL structure and indexing checks. Builds on
`docs/sticker-archive-spec.md` (the `/stickers/` collection, October 25) and
`docs/election-lifecycle-plan-20261025.md` (phases). Nothing here changes before November 4.

## Where Things Stand (Checked November 2)

- Every ready-made sticker is already a static JPG in an `<img src>` (1,191 files, 84 MB, about 72 KB each) on
  its state page, and `sitemap.xml` lists 1,271 image entries on 108 pages. Images return 200, `image/jpeg`, no
  `X-Robots-Tag`; `robots.txt` allows everything. The outside reads' main fix (real image URLs, an image
  sitemap) was done on October 25.
- One real defect, fixed November 2 (b110799): the `?v=` stamp was the file's modified time, which a CI
  checkout resets, so every build gave all 1,271 images new URLs. It is now a checksum of the file.
- Search Console, image search, October 18 to November 2: 570 impressions, 3 clicks, average position 52
  (web search: 22,777 and 2,416). Google crawls and shows the stickers; image search isn't a traffic source
  yet. The goal below is a page people use, with image discovery as a side benefit.
- Galleries already save on tap and hide stickers whose message expired (`vote_file_current`).

## 1. The All Stickers Page (`/stickers/`, Build November 4 to 6)

One page with every current VoteSticker design, classic set first, then each state A to Z. Spanish at
`/es/stickers/` once reviewed. It is the October 25 collection page, starting with our designs; official
office designs join it as their records are verified and permitted.

- **Why after Election Day:** the post-election galleries keep only the I voted family, so the page launches
  with the set that stays true, and nothing new ships on Election Day. A page built now would not be indexed
  before November 3 anyway.
- **Same tile as the galleries.** Each sticker uses the gallery tile and its save script: tap saves, the same
  file name, the same GA4 event. Under each state's row: "More [State] stickers and the maker" to the state
  page. Nothing about saving changes.
- **Ordering so neighbors differ.** Within each row, round-robin by message family (I voted, by mail,
  first-time, future voter), then by palette, then by layout, so two navy I VOTED stickers never sit side by
  side. One function, unit-tested on all 51 sets.
- **Weight.** 1,191 full-size JPGs is 84 MB. The build writes a 360px WebP thumbnail per sticker (about 10 to
  15 KB), lazy-loaded, with `width` and `height`; the tap still saves the full 1080px file. The full JPG stays
  the URL in the image sitemap.
- **Finding a state fast.** The state box at the top (the same finder) jumps to that state's row; a plain A to
  Z link list for no-JS. No filters in v1.
- **Links in.** Footer ("All stickers"), each state gallery ("See every state's stickers"), the home page's
  "Browse all states and DC" area and the history page's sticker mention. One link per page, plain `<a href>`.
- **Indexing.** Indexable, in `sitemap.xml`, canonical to itself. Image entries for its stickers point at
  `/stickers/`, while each state page keeps its own; a sticker on two pages is fine.
- **No page per sticker.** Near-identical pages for 1,191 color and layout variants would be grouped by Google
  and add nothing a visitor needs. Revisit only for designs with their own story (official office designs).

## 2. Archiving 2026 (At the December 1 Between-Elections Review)

- `/stickers/` becomes the collection with a 2026 section. A `/stickers/2026/` page is added only when a second
  year exists, as the October 25 spec says; the 2026 image URLs never move.
- Dated reminders ("Vote early Oct 19–30") stay in the archive with a label: "Past-election reminder,
  November 2026." The art is not altered. They stay out of the live galleries.
- State pages stay evergreen (`/texas/`), with "See Texas's 2026 stickers" linking to the archive section.
- Asset paths stay where they are (`assets/states/<abbr>/`); the record carries `year`, so the page groups by
  it. No file moves, no redirects.

## 3. Ready for the Next Election (Before Planning 2027 and 2028)

Today one election is written into the code: dates in `vote_phase()` and four cron lines, about 39 "Nov. 3"
style strings in `index.php` and 16 in `es.php`, the bar's storage key, and the year in 52 places in
`states.js`. The plan:

- **`build/election.json`:** election day, name, year, phase dates (pre, eday start, post), and the bar key.
  `vote_phase()`, `VOTE_ELECTION_DAY`, the Election Day bar and the share text read it.
- **Strings from the date.** "Tuesday, Nov. 3" and "Vote Nov. 3" become `vote_day()` and `vote_short()` of the
  election date in English and Spanish, so the 55 hardcoded strings go away. A QA check fails on any literal
  election date left in the templates.
- **Per-state dates stay in `states.js`**, one block per election, verified from official sources each cycle
  as now. The facts files keep their checked dates.
- **Sticker records** (`build/sticker-collection.json`) gain `year`, `message` and `palette` fields, read from
  `alts.json` and the bespoke manifest. The All Stickers page, the galleries, the ordering and the image
  sitemap read them.
- **Schedule.** The election-week crons are generated from `election.json`, or written from a checklist in the
  lifecycle doc; either way, one place to change.
- **QA by phase.** `scripts/qa_eday_bar.mjs` already switches on the date; the phase checks take the dates from
  `election.json`.

## Order and Size

| Step | When | Size |
|---|---|---|
| Post-election switch and checks | November 4 | Planned |
| All Stickers page, thumbnails, ordering, links, sitemap | November 4 to 6 | About a day |
| `election.json` and date strings | November, after the page | Half a day, mostly QA |
| Sticker records with year and message | With the page | Small |
| 2026 archive labels | December 1 review | Small |
| Official office designs in the collection | As permissions arrive | Per record |

## Check After Launch

URL Inspection on `/stickers/` and two state pages (rendered `<img src>` present), Search Console image
search by page monthly, and Ahrefs bot analytics for image requests (Garrett has the bot logging on). Absence
from Google Images is not by itself a fault.
