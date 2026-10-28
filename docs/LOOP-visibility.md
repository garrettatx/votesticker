# Loop: Visibility and Rankings

Started October 26, 2026, from Garrett's ask: use Search Console, GA4 and Ahrefs to plan and improve
content, with a standing pass on the pages that get the fewest impressions. Strategy and the data behind
it: `docs/visibility-strategy-20261026.md`. This loop takes over the content side; `LOOP-search-and-share.md`
keeps its open items (7 and 8) and `LOOP-election-prep.md` keeps the dated checks.

**Rules.** The election prep loop's rules hold: two or three pushes a day after facts, SEO, Spanish, card,
save and event QA; official sources only; Spanish per `docs/spanish-style.md`. On top of those:
- Search Console drives priority. Each change names the query or page it serves and the number behind it.
- Iterate on existing pages. One H1, a title that says what the page is, plain answers. No keyword lists,
  no repeated phrases, no FAQ blocks written for search engines.
- Measure from the recrawl date, 14 days, against the same days of the week.
- Facts only from official sources, with the quote re-read; sticker stories only from the state or county.

## Weekly Cycle (Mondays)

1. `python3 scripts/archive_search.py` (refreshes Search Console and GA4 in BigQuery).
2. Pull three lists: the 15 English pages with the fewest impressions in 14 days; queries at positions 8
   to 20 with 5 or more impressions; queries whose impressions are split over three or more pages.
3. For each low page, compare its visible queries with the strongest pages' (the top pages show what
   searchers want: "[state] i voted sticker", early voting, ballot photos). Pick one change that closes a
   real gap: a missing sticker story, a fact searchers ask for, a title that misses the state's own word
   for the sticker ("nc", "sc", "mn" abbreviations show up in queries).
4. Make at most three page changes a week, log them in `docs/change-log.csv`, and read them two weeks on.

## Items

| # | Item | Evidence | Status |
|---|---|---|---|
| 1 | Classic I Voted Stickers on the home page | "i voted sticker" 6,300 a month, KD 1, home not visible for it; "i voted early sticker" split over 23 pages | Done Oct 26 (live) |
| 2 | One line under each state gallery linking the home set, with three anchor wordings spread evenly (classic / red, white and blue / for any state). Replaced the three-sticker strip the same evening: Garrett felt it competed with the state's own stickers | National queries land on state pages (Texas takes 79 of 199 "i voted early sticker" impressions); one consistent link tells Google which page is the national one | Built Oct 26, ships with the next push |
| 3 | Home page FAQ: "Where can I get an I voted sticker?" (polling places hand them out after you vote; voted by mail or missed one, save a free one here) | "how to get i voted sticker" pos 9.3 and "where can i get an i voted sticker" pos 10.7 land on Oregon and Washington | Built Oct 26 (home page only, EN and ES), ships with the next push |
| 4 | Sticker stories for the eight states without one: AR, AZ, DE, IN, ME, NE, NJ, WI (state or county sources only; NE and IL need a person to read them) | "[state] i voted sticker" is the top query on every state page; stories are what the strongest pages have and these lack | Done Oct 26 for NE (Lancaster County), IN (2024 statewide contest), AZ (Maricopa 2024), WI (Washington County 2026). Not added: AR (official page now 404, archive only), NJ (2019, not read directly), DE and ME (no official source; Maine's town stickers appear only in news) |
| 5 | Georgia: 15 impressions at position 7.9 for a state of 11 million. Read Georgia's queries; the Secretary of State released a new 2026 sticker, so make sure the page names it and links the announcement | Lowest impressions per voter of any large state | Checked Oct 26: the page already names the 2026 Steve Penley sticker and links the Secretary of State; no change. Watch Georgia's queries in the Monday pass |
| 6 | Low-impression pass, first round: KY (3), NH (3), WY (2), ND (0), WV (8), SD (8), ID (12), DE (16), ME (19), MT (20). Most are small-demand states; check each title and sticker story, fix only real gaps | Bottom of the page list Oct 19 to 25 | Done Oct 26: no change. Eight of ten have an official sticker story and a title naming the state's voting window; DE and ME have no official source. Ahrefs puts "[state] i voted sticker" at 0 a month for all but KY (10). Recheck in the Monday pass |
| 7 | History page: 133 impressions at 8.7. Its queries are "i voted sticker history" (Ballotpedia and Wikipedia rank); add the year the sticker spread, where it started, with sources | Page 1 bottom; the history query has no tool competing | After Nov 3 |
| 8 | Spanish home: "sticker de votacion" at 2.7. Watch Spanish queries after the classic set ships; no new pages | First Spanish query with position | Watch |
| 9 | After Election Day: "i voted today sticker" (150 a month) and "i voted sticker png" (150) peak; the classic set and the cut-out PNG cover both. Confirm the post-election titles keep "I voted sticker" | Ahrefs volumes Oct 26 | Nov 4 |

## Log

- **October 26.** Pulled Search Console (Oct 19 to 25: 5,506 impressions, 609 clicks), GA4 (594 organic
  sessions, 35 sticker saves since Oct 23) and Ahrefs (volumes and the "i voted sticker" results page).
  Wrote the strategy. Built item 2; QA (facts, SEO, Spanish) passes; screenshots of Texas (phone) and
  Spanish Oregon (desktop) checked. Not pushed: today's pushes are done; it ships with the morning push.
- **October 26, evening.** Item 3: home page FAQ "Where can I get an I voted sticker?" in English and
  Spanish, home page only (state pages unchanged). Facts, SEO, Spanish and stale-text QA pass. Not pushed.
- **October 26, late.** Garrett's search for Texas "I voted early" showed the Texas page on page 2. Search
  Console has Texas at 7.2 on average for "i voted early sticker" (best 6, worst 8) and no impressions
  for "i voted early texas" without "sticker", so his search was likely a different, broader query, and
  personal results vary by location. Texas "early" impressions fell from 21 a day to 7 by Oct 25, as
  early voting runs out Oct 30. Change: while a state's early voting is open, its gallery heading reads
  "Texas I Voted and I Voted Early Stickers" and the description says "I voted or I voted early sticker";
  both revert when early voting ends (Nov 4 preview checked). Also fixed "Pick a Oregon Sticker" (an
  before vowels). Item 2 became a text link (above). QA: SEO, Spanish pass.
- **October 26, night.** Item 4: an agent searched official sources for the eight states; I re-read each
  page before adding anything (Maricopa in Chrome, Lancaster's release as an image in Chrome since it
  blocks fetches, Indiana and Washington County by fetch). Four stories added as verified sticker facts
  with English and Spanish text, and their office records in `build/sticker-offices.json` (60 of 60
  reviewed). Minor named by first name and school (Maricopa). QA: facts, Spanish, SEO pass. Not pushed.
- **October 26, 9 p.m.** Item 6: read titles and sticker stories for KY, NH, WY, ND, WV, SD, ID, DE, ME, MT.
  All but DE and ME carry an official story (ND's "You Betcha I Voted," WV's eighth-grade winner, Montana's
  2026 design); titles name the state and its early voting or Election Day. Ahrefs: "[state] i voted sticker"
  is 0 a month for nine of them, KY 10, against Georgia's 60. The gap is demand, not content, so nothing changed.

### October 28, Afternoon: Search Console and GA4 Read (Garrett's Ask)

- **Search Console, Oct 21 to 28 against Oct 14 to 20:** about 8,800 impressions and 1,010 clicks (11.5%
  CTR, average position 5.3), up from roughly 1,700 impressions. Daily impressions went 838 (Oct 21) to 2,105
  (Oct 27). 75% mobile.
- **CTR follows position, not the title.** Every state title uses the same pattern; Maryland (3.4) gets
  21%, New Jersey (3.0) 29%, Texas (6.0) 5%. Texas's own queries sit at 6 to 8 with almost no clicks ("i voted
  sticker texas" 44 impressions, 0 clicks, 7.8). No title changes: the early-voting titles flip on their
  own dates this week, and a change now would muddy the Oct 24 measurement.
- **"i voted early sticker"** (256 impressions) is still split over five pages, Texas 75 at 7.1, home 27 at
  7.2. The home link line (item 2) went live Oct 26; read it in the Nov 2 Monday pass.
- **"i voted stickers by state"** and its variants (113 impressions) land on the home page at about 6. Right page.
- **GA4, Oct 21 to 28:** 1,282 sessions, 80% organic search. 154 sticker_save, 79 sticker_save_cancel.
  Direct is 204 sessions at 18% engaged (likely app link previews or bots); home landing 205 sessions at
  39% engaged, the lowest of the top pages.
- **iPhone cancels (BigQuery, Oct 27):** 18 iOS Safari sessions closed the share sheet; 2 saved afterward.
  iOS had 14 share-sheet saves that day, so about half of iPhone share attempts end in a cancel. One day,
  small numbers; read Oct 28 when it lands. Candidate fix: after a cancel, a one-line hint under the Save
  button pointing to Save Image in the menu or press and hold.
