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
| 2 | Three classic stickers on every state page, linking to the home set ("See all 18 classic I voted stickers"); the third fits the state's rules | National queries land on state pages (Texas takes 79 of 199 "i voted early sticker" impressions); one consistent link tells Google which page is the national one | Built Oct 26, ships with the next push |
| 3 | Home page FAQ: "Where can I get an I voted sticker?" (polling places hand them out after you vote; voted by mail or missed one, save a free one here) | "how to get i voted sticker" pos 9.3 and "where can i get an i voted sticker" pos 10.7 land on Oregon and Washington | Built Oct 26 (home page only, EN and ES), ships with the next push |
| 4 | Sticker stories for the eight states without one: AR, AZ, DE, IN, ME, NE, NJ, WI (state or county sources only; NE and IL need a person to read them) | "[state] i voted sticker" is the top query on every state page; stories are what the strongest pages have and these lack | Open |
| 5 | Georgia: 15 impressions at position 7.9 for a state of 11 million. Read Georgia's queries; the Secretary of State released a new 2026 sticker, so make sure the page names it and links the announcement | Lowest impressions per voter of any large state | Open |
| 6 | Low-impression pass, first round: KY (3), NH (3), WY (2), ND (0), WV (8), SD (8), ID (12), DE (16), ME (19), MT (20). Most are small-demand states; check each title and sticker story, fix only real gaps | Bottom of the page list Oct 19 to 25 | Open |
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
