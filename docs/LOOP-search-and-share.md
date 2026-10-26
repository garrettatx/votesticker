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
| 5 | "Make a voting plan" link beside the calendar button: state's official plan page, else VOTE411 | Garrett, Oct 26: link out, don't build | |
| 6 | GA4 admin, walked through with Garrett: custom dimensions, 14-month retention, `sticker_save` key event | Event details invisible in reports since Oct 23 | |
| 7 | Search Console read of items 1-3 from their recrawl date (with the Oct 29 dated check) | | |

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
