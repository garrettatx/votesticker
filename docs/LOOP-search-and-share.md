# Loop: Search and Share

Started October 26, 2026, from Garrett's OK on the Search Console proposals (`docs/LOOP-facts-and-
polish.md`, Proposed section): match what people search for on pages that already rank, fix text
that shows up wrong in snippets, link out for planning, and get GA4 able to show whether sharing
works. Iterate on existing pages; no rebuilds. The dated checks stay in `docs/LOOP-election-prep.md`.

**Rules** are the election prep loop's: two or three pushes a day after facts, SEO, Spanish
(`--base http://127.0.0.1:8792/`), card, save and event QA; official sources only; Spanish per
`docs/spanish-style.md`; no social posts or outreach without Garrett's OK. Planning is a link to a
reputable nonpartisan resource (the state's own plan page, else VOTE411), never a built planner.
Measure each change in Search Console from its recrawl date.

## Items

| # | Item | Evidence | Status |
|---|---|---|---|
| 1 | Homepage H1 "Free I voted stickers for every state", the brand line moves to the eyebrow; title and description name I voted early | Homepage at position 7.1 for "i voted stickers by state"; H1 lacked the phrase. Garrett's OK Oct 26 |Done Oct 26 |
| 2 | Homepage "I Voted Early Stickers" section: ready-made I voted early designs linking their states | "i voted early sticker", 184 impressions a week, split across five state pages at positions 5-7 |Done Oct 26 |
| 3 | Homepage text: a space between "Nov. 3" and "8 days away"; "underway in 37 states" instead of "open today" | Snippet and screen-reader text read "Nov. 38"; Sunday closures |Done Oct 26 |
| 4 | Homepage FAQ "Do you keep my information?" matches About | Flat "No" overstates it with analytics on |Done Oct 26 |
| 5 | "Make a voting plan" link beside the calendar button: state's official plan page, else VOTE411 | Garrett, Oct 26: link out, don't build | Built Oct 26 (VOTE411 for all); official pages next |
| 6 | GA4 admin, walked through with Garrett: custom dimensions, 14-month retention, `sticker_save` key event | Event details invisible in reports since Oct 23 | Done Oct 26 |
| 7 | Search Console read of items 1-3 from their recrawl date (with the Oct 29 dated check) | | |
| 8 | Mail-ballot fix ("cure") rules for the 25 states without them (AL AR CO DC GA IA ID KY MD MO MS NC ND NE NH NM OK PA RI SC SD TN TX WV WY), official sources | Mail voters need it Nov 3-10; kept on the page after Election Day | |
| 9 | Transparent cut-out PNG for disc designs (backlog maker idea) | Sticker people can lay over a photo; "i voted sticker png" searches | Done Oct 26 |
| 10 | Official state plan pages into `where.plan_url` (item 5 part 2) | | |

## Log

- **Oct 26:** loop written; items 1-5 built tonight, pushed with the Oct 27 morning push (Oct 26's
  three pushes are used). Official plan pages researched by one agent.
- **Oct 26, items 1-4 (homepage).** Title "Free I Voted and I Voted Early Stickers by State | Vote Sticker"
  (ES "Stickers gratis de Ya voté y Ya voté temprano por estado"); H1 "Free I voted stickers for every
  state" with "Make a voting plan. Share the sticker." as the eyebrow; the description names I voted, I
  voted early and vote early. New "I Voted Early Stickers" section (English home page): eight ready-made
  designs from TX, NC, NY, FL, OH, MD, PA and VA, each linking to its state gallery, with their own alt
  text; shown before and after Election Day. A space now separates "Tuesday, Nov. 3" from "8 days away"
  (text read "Nov. 38"); "Early voting is underway in 37 states" replaces "open today". The FAQ privacy
  answer now names the analytics and points to About. Previews for Nov 2, Nov 4 and Dec 5 render the
  right H1 and section. Facts, Spanish, SEO and event QA pass; stale scan at 27. No sideways scroll at
  390 or 1280 px. Ships in the Oct 27 morning push.
- **Oct 26, item 6 (GA4 admin, with Garrett).** Event data retention 14 months; eight event-scoped
  custom dimensions (state, sticker_source, design, palette, message, save_method, post_variant,
  post_edited); `sticker_save` marked as a key event. Verified through the GA4 Data API metadata: all
  eight `customEvent:` dimensions and the `keyEvents:sticker_save` metric exist. `search_term` and
  `method` use GA4's built-in Search term and Method dimensions. Retention can't be read without the
  Admin API (not enabled on the service account); Garrett saved it. Dimensions fill from Oct 26 on,
  not backward. Still open in the backlog: GA4 to BigQuery link and Search Console bulk export.
- **Oct 26, item 5 (planning link), part 1.** "Make a voting plan ↗" ("Haz tu plan para votar") under
  the calendar button on every state page, both languages, opening in a new tab. Default:
  VOTE411's make-your-plan page ("Planning help on VOTE411, from the nonpartisan League of Women Voters
  Education Fund"; Spanish notes it's in English). A state's facts file can name its own election
  office's page as `where.plan_url` (new url fact, official hosts only); then the line reads "Planning
  help from <the office>". Gone from Nov 4 with the calendar link. Build guard: a state page that gets
  a calendar file must carry both links (mutation-tested; it stopped on Alabama). 102 of 102 state
  pages have it; Nov 4 preview has none. Facts, Spanish and SEO QA pass.
- **Oct 26, item 9 (round cut-out).** New maker option under Preview circle crop: "Round sticker with a
  see-through background (PNG)" ("Sticker redondo con fondo transparente (PNG)"), square size only,
  remembered in the browser, file name ends `-round.png`. Works for all 12 designs, not only the disc
  one: the canvas is cut to the full circle after drawing, and every design's content sits inside the
  safe circle, so no words are trimmed. Pixel probe on Texas, every design: corners alpha 0, center and
  top edge 255; Story size hides the option and keeps a solid background; unchecking restores it.
  Save, event and Spanish QA pass.
- **Oct 26, open fact call: Kansas mail deadline.** The SOS advance-voting page (read Oct 26) gives the
  statute (close of polls) and the Douglas County injunction (postmarked by Election Day, arriving by
  the last mail delivery Friday, Nov 6), and says the Court of Appeals denied a stay for the general
  election. The page now says both, attributed to the Secretary of State, with drop-off before polls
  close as the sure route and "mail it early." The page's "Beginning in 2026" sentence states the
  statute the order overrides. Re-read added to the Nov 2 dated check. Facts and Spanish QA pass.
