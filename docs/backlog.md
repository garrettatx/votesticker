# Vote Sticker Backlog

Open items, newest first. Started October 21, 2026. Move an item to `docs/history.md` when it ships.

## Outside UX Reads, Deferred (October 27)

Garrett shared four outside reads (home page, state page, picker, state switcher). Built Oct 27: maker
grouped (preview, message, layout row, colors, Save; the rest under More options), one Tilt checkbox,
"Election Day hours:" label, softer crop wording, FAQ save wording, a jump link to the voting facts,
measurement v2 (`docs/analytics.md`). Deferred, each to be judged against GA4 and Search Console first:

- **One picker for gallery and maker.** Pick a sticker, then Save or Customize it in one workspace.
  Ready-made JPGs can't be edited (only the maker-drawn bespoke ones could open in the maker), and today
  a gallery tap saves in one step; an inline reveal adds a tap. Revisit for 2028 with `sticker_builder_start` data.
- **A message filter on the galleries** (I voted, I voted early, by mail) and four to six designs with
  Show more. Gallery images are image-search assets; keep them in the HTML if collapsed.
- **Home page order:** stickers first, one state path. Hold until the Oct 26 title and I Voted Early
  section have two weeks of data (visibility loop item 1).
- **Voting facts in expandable sections.** Not now: hours, ID and dates are what voters search for and
  need at a glance this week. Consider for the post-election page.
- **Fewer state-link groups at the bottom** of state pages. Measure clicks before cutting; they're
  internal links.
- **State switcher:** a labeled native select with a Go button above the state H1, preserving the
  message. The finder near the bottom already does this by typing.
- **Countdown hardening:** recompute "N days away" in the browser so a late daily build can't leave it a
  day off.
- **Wording the reads suggested and we kept:** Save to Photos (iPhone) over "Save or Share Image";
  color swatches over a dropdown; the nudge "Send this page to someone who hasn't voted yet"; two hero
  countdowns while early voting is open.

## Cover Photos and Maker Copy (October 26)

Specs in `backlog/cover-photo-and-maker-copy/README.md`, not approved: a Cover photo output with
per-platform presets and safe areas (LinkedIn, X, Facebook Page and profile), purpose-led size labels
and copy fixes from an outside review, and the photo-upload question for Garrett.

## Maker: More Bespoke Stickers (October 26)

Specs and mockups in `backlog/maker-bespoke/README.md`. Item 2 (bespoke sets per state) shipped Oct 26; the rest not approved: a background finish picker
(Sunburst built; Glow, Dots, Watermark), pre-built bespoke sets on each state page with descriptive
image names, retiring Shadow for a tightened Ballot bubble, WE VOTED / YA VOTAMOS, a keyline border,
and renaming the Tilt tile to Slant.

## Needed Before Election Day

| Item | Why | Notes |
|---|---|---|
| **Open fact calls** | All 51 guides live October 21. These stayed off the pages because official sources disagree or couldn't be read. | NJ cure deadline, MI photo rule, KS mail return (answered Oct 26 from the SOS page: the injunction covers this election), AR in-person absentee deadlines, MT late-registration cutoff (court blocked SB 490; live answer now says ask your county), WY regular registration date (Oct 19 PDF vs. 14-day rule), WV provisional-without-ID path, VT mail deadline (statute vs. SOS page), NH thin (sos.nh.gov blocks reading), Vermont/Maine/Montana/ND/SD early windows not set (no single official date). Alaska and Maine could get a ranked-choice link to Resist Now's Learn guide. |
| **History of the I Voted sticker** | A sourced history page at `/history/`, linked from the state sticker sections, the home FAQ and About. | Plan: `docs/sticker-history-plan.md`. Waiting on Garrett's research and data; data visualization after. |
| **"Early voting has ended" family** | Texas ends October 30, others sooner. A page that still says "early voting runs" after it closes is wrong. | Plan Section 5.1 (`early_over`). Depends on the daily rebuild. |

## Election Lifecycle and Collection (October 25)

| Item | Why | Notes |
|---|---|---|
| **Election Day banner** | November 3 visitors need the polling place lookup and hours first | `docs/election-lifecycle-plan-20261025.md`. Needs verified poll hours (loop batch 3 running). Build before November 2. |
| **Voted mode** | From November 4, lead with "Voted? Get your sticker"; retire "still time to vote" post text | Same plan. Build before November 3. |
| **Remove the November 4 cron** | `30 6 4 11 *` in `.github/workflows/daily-build.yml` would fire again in 2027 with `VOTE_TODAY=2026-11-04` | After November 4, 2026. |
| **Between elections mode** | Evergreen intro, past dates collapsed, official upcoming-elections link | About December 1. |
| **Sticker collection and 2026 archive** | One page of official and VoteSticker designs with credits, distribution and permissions; replaces the Phase 3 gallery | `docs/sticker-archive-spec.md`. After November 3; data collection can start now. |
| **Snohomish County pickup** | Second Washington mail source: stickers at the Auditor's Office, Sno-Isle Libraries and voter service sites (Oct 7, 2026 release) | Washington's mail answer holds one source (Thurston). Decide whether pickup sites or packet inclusion leads, or allow two sources per answer. |
| **Descriptive image filenames** | `tx-vote-1.jpg` says nothing to image search | Only with redirects from the old URLs; after the election. |
| **Designer interviews, election office questions** | Original reporting no other site has | Outreach is Garrett's call. |

## Analytics (October 23)

Events, the share loop and the BigQuery archive are built (`docs/analytics.md`). These are Garrett's
clicks in the GA4 and Search Console admin screens:

| Item | Where | Notes |
|---|---|---|
| **Done Oct 26:** **Data retention to 14 months** | GA4 Admin, Data collection and modification, Data retention | The default is 2 months for explorations, so October's detail is gone by January. Not retroactive, so the sooner the better. |
| **Done Oct 26:** **Register custom dimensions** | GA4 Admin, Custom definitions, event scope | `state`, `sticker_source`, `design`, `palette`, `message`, `save_method`, `post_variant`, `post_edited`, `search_term`, `method`. Parameters only show in reports after this, and not retroactively. |
| **Done Oct 26:** **Mark `sticker_save` as a key event** | GA4 Admin, Events | After it has fired once on the live site (October 24 build). |
| **Link GA4 to BigQuery** | GA4 Admin, Product links, BigQuery | Project `seo-command-ctr`, daily export, location US. Raw events, kept indefinitely. Not retroactive. |
| **Search Console bulk export** | Search Console Settings, Bulk data export | Both service accounts are users already. Project `seo-command-ctr`, dataset suffix `votesticker`, location US. Adds totals, country and device for the hidden-query impressions, which the API drops. Starts within 48 hours; not retroactive. |
| **Index snapshots through Election Day** | `python3 scripts/archive_search.py --index` | Weekly by hand (about 10 minutes); the Sunday job skips it to stay short. |

## Done October 21

- Daily rebuild: `.github/workflows/daily-build.yml`, 11:05 UTC, first run passed every step. Secrets set from the environment.
- ivotedsticker.org: A records to 192.0.2.1 (proxied) and one dynamic redirect rule, all paths 301 to votesticker.org. HTTPS verified.
- Per-state mail ribbons (CA, CO, HI, OR, WA) and sticker date lines; per-image alt text for all 369 ready-mades (`site/assets/states/alts.json`).

## Content and Copy

- ~~Mail-state ribbon with real dates~~ (done).
- **Old mail-ribbon note.** Washington's ribbon reads "VOTE BY MAIL, DUE NOV 3"; the review suggested "DROP BOX BY 8 P.M. NOV 3". The ribbon comes from `states.js` for every mail state, and return rules differ (postmark vs. received), so this needs a per-state ribbon field, not a blanket change.
- ~~Per-image alt text~~ (done). Old note: ("Plum circle sticker reading VOTE EARLY, Texas, Oct. 19–30"). Needs the printed text per image; the Codex prompt files list it for the Codex sets.
- **Mail-state default date line.** Washington's maker starts on "DUE NOV 3"; "BY 8 P.M. NOV 3" fits better. Same per-state field as the ribbon.
- **"Signed, sealed, delivered" headline:** declined. Three lines over the 10-character limit, and it's a song title. "MY BALLOT / IS IN" shipped instead.

## Tool Ideas (Brand Brief)

- A "Done" line after switching to I voted: "Send this to someone who hasn't voted yet." Share link only, no tracking.
- An add-to-calendar file for your voting day, built in the browser. Nothing stored.
- "Early voting ends in N days," shown only when the verified dates make it true (needs the daily rebuild).

## Maker Ideas From the Sticker History Research (October 22)

Already in the maker: square and story sizes, native share on phones, a free-text date line, I voted and
I'm voting messages, Spanish. Worth adding, in rough order of value before November 3:

- **"First time voter" message** (FIRST TIME / VOTER, and PRIMERA VEZ / VOTÉ or similar after Spanish review). The research's strongest new message; fits the 10-character line rule.
- **See it at profile-picture size.** A small 48 px circle preview next to the big one, so people can check it reads where friends will see it.
- **Copy alt text.** A button that copies a plain description of the sticker ("Plum sticker reading VOTE EARLY, Texas, Oct. 19–30") for people who add alt text to posts.
- **Cut-out sticker (transparent PNG)** for laying over a photo. Disc designs only; square designs have no edge to cut.
- **Portrait 1080 x 1350** for Instagram feed posts, beside square and story.
- **"Future voter"** for kids and families. Low priority; nice after the election too.

Not doing: counters, event tracking, stickers-made maps, uploads or AR filters. They add tracking or
moderation, and the brand promise is that nothing you make leaves your device.

## Sticker Designs (October 22)

**Shipped:** Shadow, Ring and Half replaced Poster and Stamp, so the row is twelve tiles (four even
rows of three). Garrett chose them from twelve flat sketches; they were picked for what no other tile
does: a hard offset shadow, type set around a disc (state name over the top, ELECTION 2026 under the
bottom), and a color field with the state knocked out. Ring sizes its stack to fill the inner ring so
it reads at 40 px.

**Rejected, and why:** a first round imitating printed stickers (Die cut with a kiss-cut keyline,
a scalloped Seal, Script lettering over block capitals, a Vertical state name). Garrett: these are
stickers people save, not print, so paper effects read as a pin or a label; overlapping script and
block lines fused at small sizes. Their layouts, and the other nine sketches (Band, Watermark, Stack,
Check, Label, Diagonal, Pills, Hollow, Monogram), stay in `site/app.js` under `REVIEW`, out of the
row. Render any of them with `node scripts/sheet_new.mjs <out> band,stack,...`.

**Renderer additions, usable by any design:** letter spacing, leading and justified stacks in
`textBlock`; outline lettering; type on a circle (`arcItems`); rotation for every item kind; items
marked `nofit` (full-bleed bands and watermarks, drawn but not fitted to the safe circle); a `tilt`
option on `render`.

Next, if wanted:

- **Tilt control.** `render` already takes `tilt` for any design. A three-way control under the
  swatches (Straight, Left, Right at about 7 degrees).
- **Stack (sketch 4)** as a fix to Big type: every line the same width is what makes the ready-made
  lockups look designed.
- **Cover photo (nice to have).** A 1584 x 396 canvas with a 640 x 312 safe center (LinkedIn full
  width, Facebook's phone crop), the Side by side lockup fitted to a rectangle (`fit` needs a
  rectangular mode). Save as `vote-...-cover.png`.

## After the Election

- **Privacy and accessibility statements.** A footer review suggested Privacy, Terms and Accessibility links. Today the About page covers privacy in one line ("Your sticker is made on your device and is never uploaded"). After November 3, consider one short "Privacy and Accessibility" section on About rather than new thin pages.
- **"Submit a sticker."** Suggested for the history collection; needs a rights and moderation plan first.

## Design and Type (Review of October 21)

The review recommended Fraunces + Public Sans and warned against common AI-template fonts. The site
uses none of those: Anton (display, matching the stickers) and the device's system font for body text.
Switching the display face to a serif would split the site from the stickers, so it stays. The cheap,
evidence-backed parts are worth doing:

- `font-variant-numeric: tabular-nums` on the dates table and state status lines.
- Body text in the guide sections at 17px, line-height about 1.6.
- Check the muted gray (`--muted`) used for source lines and status labels against WCAG AA on cream.
- Confirm every tap target is at least 44 px (the card QA already checks buttons; swatches and chips were sized to 44 and 40).
