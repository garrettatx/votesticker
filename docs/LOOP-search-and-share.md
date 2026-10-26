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
| 5 | "Make a voting plan" link beside the calendar button: state's official plan page, else VOTE411 | Garrett, Oct 26: link out, don't build | Done Oct 26 |
| 6 | GA4 admin, walked through with Garrett: custom dimensions, 14-month retention, `sticker_save` key event | Event details invisible in reports since Oct 23 | Done Oct 26 |
| 7 | Search Console read of items 1-3 from their recrawl date (with the Oct 29 dated check) | | |
| 8 | Mail-ballot fix ("cure") rules for the 25 states without them (AL AR CO DC GA IA ID KY MD MO MS NC ND NE NH NM OK PA RI SC SD TN TX WV WY), official sources | Mail voters need it Nov 3-10; kept on the page after Election Day | Done Oct 26: 15 of 25 |
| 9 | Transparent cut-out PNG for disc designs (backlog maker idea) | Sticker people can lay over a photo; "i voted sticker png" searches | Done Oct 26 |
| 10 | Official state plan pages into `where.plan_url` (item 5 part 2) | | Done Oct 26: MD, ND; rest VOTE411 |
| 11 | Two new maker designs from six sketches: Varsity and Masthead (Garrett's picks Oct 26; Tricolor cut for its crop) | Garrett: "really make us shine" | Done Oct 26 |
| 12 | Bespoke sets: 11 maker-drawn stickers per state, English and Spanish, on every state page; best picks on the home page | Garrett: variety, more indexed images, Spanish too | Done Oct 26 |
| 13 | Clearer maker labels; 12 tiles (Shadow, Map below, Split out; Check back) | Garrett: outside review of the maker; "aiming for 12 for a good grid" | Done Oct 26 |

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
- **Oct 26, item 10 (official plan pages).** An agent checked all 51 election offices; none has a
  current page built only for planning. Re-read and added two: Maryland's 2026 "My Election Checklist"
  PDF ("There are three ways to vote (early, by mail, or on Election Day), so use this checklist to
  start making your plan.") and North Dakota's voter page with its current "Prepare - Plan -
  Participate!" block ("This printable handout explains how you can make your plan to vote").
  Turned down: California (`make-plan-vote` is now "Vote Early, California!" with no planning text),
  Idaho ("Make A Plan To Vote" is only a pamphlet chapter title), South Carolina (an early-voting press
  release that ages out Oct 31), New York, Ohio and Arizona (403 to curl; unverified). Every other
  state keeps VOTE411. Facts and Spanish QA pass.
- **Oct 26, item 8 (mail ballot fixes).** 15 states added, EN and ES, every quote re-fetched and matched
  word for word: fix processes with deadlines for CO (11:59 p.m. Nov 12, moved from Nov 11 by the
  Veterans Day rule), MD (10 a.m. Nov 13), NC (noon Nov 10, under S.L. 2026-60, which changed third to
  fifth business day; witness errors can't be fixed), ND (before the Nov 16 canvass), RI (4 p.m. Nov
  10, received not postmarked) and TX (Nov 9, with the corrective action form); process without a date
  for DC (the letter's deadline; the calendar and FAQ disagree), GA (deadline unread, sos.ga.gov 403),
  PA (county by county, provisional ballot as fallback); narrow ones for KY (signature mismatch, before
  polls close; unsigned envelopes rejected) and IA (until polls close); and "check your envelope, no
  fix" for AL, MO, SC and SD. The six dated ones carry past-tense text after their deadline (checked
  in a Nov 17 preview: no present-tense deadline left). Left out: AR (ID-only 2023 rule), MS (rule in
  a "proposed" folder), NM (no date), WV (guidance, not statute), NE, NH, OK, TN, ID, WY (no official
  statement either way). The Spanish check caught "Ballot by Mail Tracker" in the TX text; reworded.
  Facts, Spanish and SEO QA pass; stale scan at 27.
- **Oct 26, push 4 (14:01 CDT), at Garrett's request:** the homepage search changes, planning link,
  round cut-out, Kansas mail deadline and 15 states' mail-ballot fixes, after card (81,120 cards),
  save, event, facts, Spanish and SEO QA. Build dispatched by hand once so it's live today.
- **Oct 26, item 11 (two new designs).** Six sketches rendered on review sheets (ballot bubble,
  varsity, rays, tricolor, spotlight, slab; every message, palette, Spanish, 60 and 40 px). Garrett
  picked Varsity, Tricolor and Slab and leaned toward dropping Tricolor; the 40 px sheet agreed (its
  bands crop to slivers in the circle), so the maker gets two: **Varsity** (Deportivo), jersey lettering
  with an 18-step 45-degree extrusion in the accent and a hairline of the background, deepened after a
  side-by-side showed it too close to Shadow; and **Masthead** (Periódico), Alfa Slab One with a tracked
  date line between hairline rules as wide as the stack. Alfa Slab One self-hosted
  (`assets/fonts/alfa-slab-one.woff2`, latin subset, 11.6 KB, OFL; noted in docs/history.md) and loaded
  with the other faces; with the file blocked the maker still loads all 14 tiles, no errors. Rays,
  Ballot bubble, Tricolor and Spotlight stay in REVIEW. Card QA 94,640 cards in Chromium and WebKit, 0
  outside the safe circle; save, event, facts, Spanish and SEO QA pass. Desktop shows 14 tiles in rows of
  three (last row two). Next, if Garrett wants it: Rays as an optional background in the color picker.
- **Oct 26, item 11b (sunburst background).** The Rays sketch became an option, per Garrett: "Sunburst
  background" ("Fondo con rayos de sol") under the color name. 24 rays from the card's center in a
  shade of its own background (13 percent; 6 on light backgrounds, where 13 read gray and busy),
  behind everything, in Square, Portrait and Story, with the round cut-out too; on Sticker the rays
  also go inside its disc. Ring's own disc covers them. Remembered in the browser; file name gets
  `-sunburst`. Card QA 94,640 cards, 0 outside the safe circle; save, event, Spanish and SEO QA pass.
  Grid stays at 14 (Garrett: the short desktop row isn't a big deal). LAN preview for Garrett:
  http://192.168.1.252:8793/ (python http.server on dist, this session only).
- **Oct 26, homepage showcases and copy (Garrett).** Find a Sticker You Like and I Voted Early Stickers
  now show different states (16 in all: CA CO NY NC PA TX VA WA, then FL MD MI MN NJ OH SC WI),
  alphabetical by the name in the page language, each with "Don't see your state? See all 50 states
  and DC ↓" to the state list (`#states-h`). "First time voter" is now "First-time voter" (sticker
  line FIRST-TIME, 10 characters; message id unchanged). Copy scan: Election Day capitalized all 129
  times, "drop box" two words wherever a voter reads it. Spanish and SEO QA pass.
- **Oct 26, item 12 (bespoke sets).** How to rebuild: `python3 -I build/build_static.py`, serve `dist` on
  8792, then `node scripts/render_bespoke.mjs` (all 51, or `TX,NC`). It writes 11 JPGs per state into
  `site/assets/states/<abbr>/`, named `<state-name>-<message>-<design>-sticker.jpg`, deletes that
  state's earlier bespoke files first, writes alt text to `alts.json` (Spanish as `<stem>@es`) and the
  recipe of every file to `assets/states/bespoke.json` (lang, slot, design, palette, headline, tilt,
  rays). Recipe: states sorted by name, index i; design for slot j is DESIGNS[(i + 2j) mod 13] (13
  designs, no Shadow), so a state's 11 are all different and neighbors shift; palettes rotate, slot 0
  and the Spanish early/mail slot use state colors. English slots: I VOTED (state colors), FIRST-TIME
  VOTER, FUTURE VOTER, YA VOTÉ (shared with the Spanish page), I VOTED EARLY or BY MAIL or I VOTED,
  a tilted I VOTED. Spanish slots: MI PRIMER VOTO, YO TAMBIÉN VOTARÉ, YA VOTÉ TEMPRANO or POR CORREO or
  YA VOTÉ, a tilted YA VOTÉ, a sunburst YA VOTÉ. Sunburst and tilt alternate by state. Totals: 561
  (255 English, 255 Spanish, 51 shared); the image sitemap lists 1,121 images. JPEG 0.86, about 100
  KB each; `site/assets/states` is 55 MB.
  Pages: English galleries list the state's own ready-mades first, then its six; Spanish pages get a
  gallery for the first time (six Spanish stickers, Spanish alt text). After Election Day the state
  hero becomes slot 0 (I VOTED in state colors), and FIRST-TIME VOTER, FUTURE VOTER, YA VOTÉ, MI
  PRIMER VOTO and YO TAMBIÉN VOTARÉ stay in galleries (`vote_file_current`). Home: the showcase and
  I Voted Early sections and two of four hero stickers are hand-picked bespoke files (from a contact
  sheet of all candidates), eight designs and five messages; the Spanish home showcase picks one
  Spanish sticker per state, never repeating a design.
  Guards: `build/bespoke_check.py` runs in the build and stops it if voted-early appears outside a
  verified early-voting state, by-mail outside an all-mail state, a Spanish message on an English
  sticker or the reverse, a design repeats in a state, Shadow appears, a file or alt is missing, or a
  file isn't in the manifest (three planted errors, all caught). `image_names.py` skips files already
  named for people. GA4: gallery saves of bespoke stickers send `sticker_source: bespoke` and the
  design (event QA checks it), so ready-made, bespoke and maker saves can be compared.
  QA: facts, Spanish, SEO, event, save pass; Nov 4 preview checked (Texas: 9 in the English gallery,
  6 Spanish, hero is the bespoke I VOTED); stale scan at 27. Alt text fixed to avoid "slanted
  headline, tilted". Card QA below.
- **Oct 26, item 13 (labels and the 12 tiles).** Labels, EN and ES: What should it say? (¿Qué quieres que
  diga?), Dates or extra text, Reset dates, Choose a Layout, Profile picture / Post / Story (Foto de
  perfil / Publicación / Historia), Level / Tilt left / Tilt right, Show the profile-picture crop, Round
  sticker, no background (PNG), Caption to share with your sticker, Copy caption and link; the two FAQ
  answers that named "the Main message menu" now point to What should it say?. Size buttons are equal
  width with smaller text under 400 px (91 x 62 at 320 px in both languages, one line from 390).
  Design names under the thumbnails unchanged. Kept on iPhone: Save to Photos (it is the share sheet).
  Tiles: Garrett agreed to trim the look-alikes and asked for 12 for even rows. Shadow (Varsity does it
  better), Map below (Side by side) and Split (Half) moved to REVIEW; they still draw for saved links and
  their bespoke stickers stay. A Ballot bubble rework (a ballot card, then with a check in the oval) was
  rendered and sent; Garrett ruled it out as not profile-picture friendly, and its code was removed. Of
  six candidates rendered at full size and 40 px (Check, Pills, Spotlight, Monogram, Hollow, Stack), Check
  came back: the only design built on a symbol, readable as a check at 40 px; Spanish name Palomita.
  A design saved in the browser that left the row falls back to Tilt (tested with Shadow saved).
  The pre-made sets are not re-rendered for Check (Garrett: not every pre-made needs it).
