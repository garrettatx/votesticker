# Loop: Election Week and After

Started October 25, 2026, at Garrett's request: prepare for November 2, November 4 and the months
after, then keep working the backlog in order of value to visitors. One item per pass. Log each pass
at the bottom with the date, what changed, and what's next.

**Goal of every item:** a voter who lands on a page gets something true and useful for that day:
the right sticker, the right dates, the official link. Nothing expired, nothing invented.

**Rules for the loop**
- Push to main two or three times a day at most; a push is a deploy at the next build (or now, with
  `wrangler pages deploy`). Run facts, SEO, Spanish (`--base` local), card and event QA first.
- Facts only from official sources; anything uncertain stays off the page and goes to Open Fact Calls.
- No DNS or Cloudflare changes, social posts or outreach without Garrett's OK.
- No Firecrawl; use WebSearch, WebFetch or Claude in Chrome.
- Spanish strings follow `docs/spanish-style.md`.

## Before November 2

| # | Item | Status |
|---|---|---|
| 1 | PA and KS "I voted early" stickers: replace the "BY" date line with the state name | Done Oct 25 |
| 2 | Previews of the Nov 2 and Nov 4 builds (TX, WA, KS, Spanish home) sent to Garrett | Done Oct 25 |
| 3 | Post-election checklist (`docs/post-election-release.md`) checked against the build; build what's missing (post-election home FAQ) | Done Oct 25 |
| 4 | "Build and QA, no deploy" input on the daily workflow, so the Nov 4 run can be tested once ahead | Built Oct 25 |
| 5 | "First time voter" message in the maker (English and Spanish) | Done Oct 25 |
| 6 | Ballot trackers: confirm IN; look for IL, MO, WY | Checked Oct 25: none to add |
| 7 | Open fact calls that touch the Nov 2 banner (NJ, MI, KS, VT, WY, MT) | Banner scan clean Oct 25; expired-date sweep done |
| 8 | Mail-state ribbon and date line field (WA "BY 8 P.M. NOV 3") | Already done (ribbon field and `dateLineMail`) |

## Dated Checks

| Date | Check |
|---|---|
| Daily Oct 26 to Nov 1 | After the 6:05 a.m. build (5:05 from Nov 2): states whose early voting ended show the ended title, gallery and share image live; IndexNow for changed pages |
| Oct 29 | First impact look: Search Console impressions for the 21 pages updated Oct 24, from each page's recrawl date |
| Nov 1 (Sunday) | Index snapshot, `archive_search.py --index` |
| Nov 2 | Re-read sos.ks.gov/elections/advance-voting-information.html: the Kansas mail answer rests on a court order (a higher-court ruling would change it). GitHub runs the cron hours late: if no build by 7 a.m., dispatch it by hand. Banner live: hours, lookups, mail return lines; the 15 states still voting early that day keep their early-voting lines |
| Nov 4 | 06:30 UTC run deployed post-election mode (GitHub may start it hours late; dispatch with `2026-11-04` by 7 a.m. if not); spot-check EN and ES; rerun from Actions with `2026-11-04` if not |
| Nov 5 | Remove the `30 6 4 11 *` cron; log the release in `docs/change-log.csv` |

## After the Election

| # | Item | Status |
|---|---|---|
| 9 | Between-elections mode built behind a phase flag, ready for the Dec 1 review | Built Oct 25 (off) |
| 10 | Sticker collection and 2026 archive (`docs/sticker-archive-spec.md`): data first | Data built Oct 25; all 51 office records reviewed Oct 26 |
| 11 | Descriptive image filenames with 301s from the old URLs | Map built Oct 26; switch after Nov 5 |
| 12 | Snohomish County as a second Washington sticker source | Done Oct 26 |
| 13 | Privacy and accessibility section on About | Done Oct 26 |

## Log

- **Oct 25:** Loop started.
- **Oct 25, item 1:** Early-voting deadline lines changed from "BY OCT 27" to "THROUGH OCT 27" (ID, KS, ND, PA, VT). Official
  English pages say "through" for the last day of a window (NY State "October 24 through November 1"; Lake County, IL "run
  through Monday, November 2"; pa.gov "all in one visit through Oct. 27"); "by" reads as a deadline. Spanish keeps "HASTA EL
  27 OCT", which Lake County's Spanish page uses ("hasta el lunes, 2 de noviembre"). Past-tense messages (I voted early, Ya
  voté temprano) show the state name and year instead of a deadline. Postal codes in alt text keep capitals (PA 2026).
  Hand-made "Vote early, by Oct 27" pictures stay; the gallery hides them once early voting ends.
- **Oct 25, item 2:** Previews of the Nov 2 and Nov 4 builds sent. Fixed what they showed: WA and CO Nov 2 banners still said
  to mail by Oct 27 (now `until` + after-wording, like OR); `vote_fact()` used the clock instead of `vote_today()`, so preview
  builds never showed after-wording; "Washington I / Voted" and "Ya / voté" headings now keep those words together.
- **Oct 25, item 3 (part):** From Nov 4, state FAQs drop poll hours, ballot and registration deadlines, lines and late mail
  (page and FAQPage markup); ID, ballot photos, provisional, tracking and envelope questions stay. Two new questions from the
  copy deck (EN and ES). Hours-vary and no-early-voting questions hidden after Election Day.
- **Oct 25, item 4:** `dry_run` input on the daily workflow (build and QA, no deploy, IndexNow or commit) and a step that
  fails the run if the pages weren't built for the expected phase.
- **Oct 25, item 4 check:** Dry run dispatched as 2026-11-04 (run 40258878715): build, local QA and phase check passed ("phase
  post"); deploy, live QA, IndexNow and commit skipped.
- **Oct 25, item 5:** "FIRST TIME / VOTER" (English) and "MI PRIMER / VOTO" (Spanish, natural "my first vote"; "PRIMERA VEZ"
  runs over the 10-character line) in the maker, with the state name and year on the date bar; post text uses the I voted
  version. Stays after Election Day. Card QA: 68,880 cards, none outside the safe circle.
- **Oct 25, item 6:** No tracker to add. IN: in.gov's absentee page names none (the portal shows registration and
  applications); IL: no statewide tracker (SB 60 and earlier bills didn't pass as far as found); MO: none, ask the local
  election authority; WY: sos.wyo.gov sends voters to the county clerk. Pages keep no tracker link for these four.
- **Oct 25, item 7:** All 103 Nov 2 banners scanned: no October dates, no today/tomorrow. New `scripts/qa_stale.py` flags
  forward phrases with a past date. It found 15 already-wrong "Can I still register?" answers (AL, PA, SD, NY, MA, NE as
  "No"; CA, IA, ID, IL, MI, UT, VA, WY past tense) and fixed them now; CO, WA, WI, NC get `until` after-wording in
  `build/faq_text.py` (new optional third item, `vote_faq_text()` in index.php). Early-voting text with after-wording: AK,
  CA, MN, NJ, NM, NY (3 facts), UT, AZ mail return; HI and VT past tense; IL note drops "now"; related-states heading says
  "Began" once the date passes. Scan after: Nov 3 and Nov 4 clean apart from VT's past-tense line (scan false positive).
- **Oct 25, item 3 (rest):** Nov 4 phrase search found voting instructions still on state pages. From Nov 4 the guide keeps
  the past-dates table (no registration help link), "Check or Fix Your Mail Ballot" (cure fact and tracker only) and the
  state's sticker section; Where You Can Vote, What ID to Bring, Phones/Photos/Gear and who-can-vote-by-mail leave. The
  section heading becomes "Official Election Information". Image sitemap on Nov 4 lists only the shown stickers. Left as
  is: after-wording inside the past-dates table (AL "Plan to vote...") and sticker-history answers about early voting.
- **Oct 25, item 8:** Already in place: WA ribbon "DROP BOX BY 8 P.M. NOV 3" and mail-state `dateLineMail` in the maker.
- **Oct 25, item 9:** Between-elections mode behind `VOTE_BETWEEN=1` (off). It keeps the post pages and drops the 2026
  past-dates table, the mail ballot fix and tracker section, the tracker line in the summary box, and the tracking,
  unsigned-envelope and provisional FAQ answers. Intro reads "Free Texas I voted stickers..." and the home H1 "Free I
  voted stickers for every state" (ES too). Workflow: `between` dispatch input, and the repository variable
  `VOTE_BETWEEN=1` keeps it on for the daily runs; the phase check expects post. Fact QA allows a page down to the
  official link in this mode. For the Dec 1 review: some `officialUrl` links point to 2026-specific pages (WI goes to
  in-person absentee voting); each state needs an evergreen official link before the switch.
- **Oct 25, item 10 (data):** `build/sticker_collection.py` writes `build/sticker-collection.json`: 477 VoteSticker
  designs (168 already voted, 27 planning, 282 dated reminders; placement follows the same test as the gallery
  after Election Day) and 40 election office records built from the verified sticker facts (story plus early-voting
  and mail-packet pickup, each with source, quote and checked date). Designer, office, selection, year and image
  permission are blank until filled in by hand in `build/sticker-offices.json`; every record says no permission and
  no image. `--check` fails on unknown hand fields, hand records with no verified facts, and permission without its
  grant (mutation-tested). Not used by any page yet. Next: review the 40 office records against the spec's 13-state
  starting table (MI, ND, KY, NH, AL, MT, CO not yet matched to a design record).
- **Oct 26, item 10 (review, part 1):** `build/sticker-offices.json` added: hand fields (title, year, designer,
  office, selection, elections) for 12 office records, taken only from each record's verified story (checked on
  the official page Oct 21 to 24): AK (2022 contest), CO, GA, KY, LA, MI, MT, ND, OH (2019), TN, VT, WV. Minors
  by first name and school (WV, OH), per the spec. Permission stays none on all. Keys starting with `_` are
  notes and skipped; the three hand-file guards re-tested and fire. Spec states with no verified story yet: NH
  and AL (sos.nh.gov PDF, sos.alabama.gov) need reading on the official pages before a record exists; AK's 2026
  pamphlet artist is also unread. Next: those three, then the 28 records with distribution facts only.
- **Oct 26, item 10 (review, part 2):** Alabama: new official release (sos.alabama.gov; no date on the page, search listing says Oct 21) on
  "newly designed America 250 \"I Voted\" stickers" for the Nov 3 general. Added as a verified sticker fact
  (EN and ES), so the Alabama page now has a sticker story, plus the `al-office` record (41 records). The
  release doesn't say who designed it or where voters pick it up, so the page and record say neither.
  Facts, SEO and Spanish QA pass locally; the stale scan's 27 hits are open windows that started earlier
  (same as before the change). New Hampshire's site returns 403 to fetch tools; read it in Chrome next.
- **Oct 26, item 10 (review, part 3):** New Hampshire read in Chrome (sos.nh.gov blocks fetch tools): the 2026
  contest for fourth and fifth graders, theme "Celebrating 250 Years of New Hampshire History!", winners picked
  by a panel of local election officials and "distributed as stickers to all voters during the 2026 state
  elections." Added as a verified sticker fact (EN and ES) with the source name, so the NH page has a sticker
  story for the first time, plus `nh-office`. The page says four winners and the spec's Perplexity row said six
  (from a Nov 2025 PDF, unread), so neither page nor record gives a count. QA passes. Left from the spec table:
  AK's 2026 pamphlet artist. Next: that, then the 28 distribution-only records.
- **Oct 26, item 10 (review, part 4):** Alaska's Sept 10 release ("Division of Elections unveils cover art and
  stickers for 2026 election") is two images with no page text; read the first in Chrome. Anchorage artist
  Amanda Rose Warren's "Under One Aurora" (humpback whale, moose, fox, musk ox, bear for the five regions, one
  aurora, US and Alaska flags) is on the 2026 "I Voted" stickers. The Alaska page's sticker story now says that
  (EN and ES) instead of the 2022 youth contest; `ak-office` is now the 2026 record. The quote's source image is
  named in the fact's notes. QA passes. Spec table done: all 13 states have a record or a page fact. Next: the
  28 distribution-only records, then item 11.
- **Oct 26, item 10 (review, part 5):** The other 28 office records reviewed from their own quotes. Ten
  records held two different stickers (a state or county story plus another county's pickup fact: FL
  Sarasota and Clay, MO Lincoln and St. Louis, NC Durham and Guilford, NM Santa Fe and Los Alamos, NY
  Ulster and NYC, KS state and Sedgwick, OR state and Multnomah, RI in person and "I Voted by Mail", CA
  San Luis Obispo and San Diego). A `split` list in the hand file now makes each such fact its own
  record (`fl-early`, `ks-mail`); 51 records, all reviewed, 23 with a county. Office stays blank where
  the quote names none (NYC, San Diego, Clark, Johnson, Gaines, Roanoke, Los Alamos, Guilford, Multnomah).
  Rule-only records (CT and MN law, the ID, KS, OK and RI voting steps) keep no design fields. New guard:
  a split use other than early or mail fails, and a forgotten split leaves its hand record orphaned,
  which fails too (both fired). Facts QA passes; no page reads the file yet. Next: item 11 after the
  build check.
- **Oct 26, item 11 (map):** `build/image_names.py` writes `build/image-names.json`: all 477 ready-made
  stickers get a name from their alt text, state, message, color and outline (`tx-vote-7.jpg` becomes
  `texas-vote-early-sticker-light-blue-outline.jpg`), with each state's gallery order and its
  `_redirects` 301 line. Dates, years and postal codes stay out of the names, since the files outlive
  the election; repeats get -2, -3. Ten messages across the set (i-voted 130, vote-early 128,
  vote-election-day 105). `--check` fails on a duplicate, a date, a non-lowercase name or an unchanged
  name (all four fired). Nothing renamed: index.php builds `<abbr>-vote-<n>.jpg` in the share image,
  hero and showcase, sorts the gallery by the number in the name and keys alts.json by the old stem;
  history.php, the collection ids and render_readymades.mjs use the old names too. The docstring lists
  each. The switch waits until after Nov 5, as the backlog says. 477 static redirects fit Pages' 2,000.
- **Oct 26, item 12:** Snohomish County's Oct 7 release (read from the page's own text) is now
  Washington's sticker story, which was empty: seven new I Voted designs of the county's landscapes,
  industries and cultures, one made with the Tulalip Tribes, at the Auditor's Office in Everett,
  Sno-Isle Libraries and accessible voter services sites once your ballot is in (EN and ES). It took
  the story slot, so the mail answer keeps one source (Thurston) and no answer needs two. `until`
  Nov 3 with past-tense after-text ("For the November 3, 2026 election, Snohomish County offered..."),
  checked in a Nov 4 preview build in both languages. The release names no drop boxes, so the page
  doesn't either. Collection: `wa-office` is Snohomish, Thurston split out as `wa-mail` (52 records).
  Facts, Spanish and SEO QA pass; the stale scan is unchanged at 27.
- **Oct 26, item 13 (About: Privacy and Accessibility).** New section on `/about/` and `/es/about/`,
  written from what the code does: the sticker is drawn on the device and never uploaded, no photo,
  name or email asked for; Google Analytics (cookies) and Ahrefs Web Analytics; events carry the state,
  design and message, never post words; a state-finder search that matches nothing is logged; the
  browser remembers design, color and language. Accessibility: alt text on every ready-made, keyboard
  controls with a focus outline, reduce-motion honored, and a contact link for problems. The old
  "basic, anonymous page analytics" line came out: GA4 sets cookies, so "anonymous" overstated it.
  To make the reduce-motion line true, the maker's scroll to the preview now skips smooth scrolling
  when the device asks for less motion (`app.js`).
- **Oct 26, bug found during item 13: PHP warnings on ID, ND, PA and VT (EN and ES), live since Oct 21.**
  These four states have no single start date, and `vote_post_fact()` read `$st['start']` anyway.
  PHP printed about 15,000 characters of warnings, with the server path, into the `data-posts`
  attribute, so `JSON.parse` failed and the Short, Friends and Official post versions did nothing on
  those eight pages. Under the warnings, those versions read "runs January 1 to October 30". Fixed:
  no start date means "runs through <end>" ("sigue hasta el <end>"), the same wording as the sticker's
  THROUGH line. New guard in `build/build_static.py`: any PHP Warning, Deprecated, Notice or error in
  a render stops the build (mutation-tested against the old code; it stopped on Idaho). Builds for
  Oct 31, Nov 2 and Nov 4 render clean. Chip switching checked in a browser on Idaho and Spanish
  Vermont. Facts, Spanish, SEO and event QA pass; the stale scan is unchanged at 27.
- **Oct 26, push 1 of the day (07:50 CDT):** 10 commits to main after card QA passed (68,880 cards in
  Chromium and WebKit, 0 outside the safe circle). They deploy at today's scheduled build; the dated live
  check follows it.
- **Oct 26, dated check (daily build).** GitHub hadn't started the 11:05 UTC scheduled run by 16:34
  UTC, the latest all week, so I dispatched it by hand (run 40360397551, success, deployed 16:35 UTC).
  IndexNow took 108 URLs (HTTP 200). Live: no PHP warnings on ID, ND, PA or VT in either language,
  and their post data parses; Texas has the Early voting ends line, the calendar link (`/cal/texas.ics`
  served as text/calendar, noindex), the in-line rule, the tilt, portrait, copy-description and small
  previews, and the El Paso sticker story; About has Privacy and Accessibility in both languages; the
  TX hero still reads EARLY VOTING OCT 19-30. No state's early voting has ended yet (LA and PA end
  Oct 27, so the Oct 28 build is their first ended day). WA and CO ribbons read DROP BOX BY 8 P.M. NOV
  3 and TURN IN BY 7 P.M. NOV 3. Next: Oct 27 daily check (dispatch by hand by 7 a.m. if the cron
  hasn't run), Oct 29 Search Console look.
- **Oct 27, dated check and push 1 of the day (06:32 CDT).** The 11:05 UTC scheduled build hadn't
  started by 11:31, so I pushed the six local commits (visibility loop, home FAQ, classic text link on
  state pages, I voted early heading, four sticker stories, item 6 log) and dispatched run 40496940990:
  success, local and live QA PASS, IndexNow HTTP 200. Before the push: facts, Spanish, SEO, events and
  save QA pass; 27 stale sentences as expected; card QA not rerun (no renderer change since the last
  pass). Live: Texas gallery reads "Texas I Voted and I Voted Early Stickers"; the classic link shows
  under each state gallery; the home FAQ is up; NE, IN, AZ and WI show their stories. LA ("Early Voting
  Oct 20–27") and PA ("Through Oct 27") still read open today as they should; tomorrow's build is their
  first ended day. Next: Oct 28 check for LA and PA, Oct 29 Search Console look.
- **Oct 27, afternoon: maker, measurement and content fixes (push 2 of the day).** From Garrett's questions
  and four outside UX reads (deferred ideas and pushback in `docs/backlog.md`). GA4 (Oct 26 to 27, after
  source tagging began): about 2 to 3 percent of visits save a maker sticker, about 8 percent a ready-made.
  Built: the grouped maker (preview, message, a sideways layout row on phones, colors, Save; the rest under
  More options with a sticky live thumbnail on phones), one Tilt checkbox, "Cut out the circle" with a
  plain explanation, measurement v2 (`docs/analytics.md`), "Election Day hours:" on every state's info
  block (it sat under "Vote early" as if it were early voting hours), the vote-by-mail summary bug (CA, CO,
  HI, OR, WA showed "ID: Official info" above Save and in the preview alt text), Title Case info headings,
  spacing fixes found by a gap audit at 390, 820 and 1024 px (a 514 px blank under the caption at 1024),
  the home "I Voted Stickers by State" heading. QA: facts, Spanish, SEO, events (new cases: double click,
  retry after cancel, open-sheet tap, share error, analytics blocked), save, devices (one WebKit prefetch
  flake on iPad Mini, passed on rerun), cards (0 outside the safe circle); the download stays the full
  1080 square. Garrett to register `attempt`, `prev_result`, `first_action` in GA4.
- **Oct 27, evening (Garrett's request, ideation):** 24 sticker ideas for the ready-made sets: 12 with the state
  (Postmark, Marquee, Ribbon, Arcs, Speech bubble, Stencil, Orbit, Horizon, Pixel state, Scoreboard, Neon,
  Blueprint) and 12 general (Ballot box, Wave, Fireworks, Confetti, Calendar, Megaphone, Done, Echo, Pixel
  check, Chrome, Dusk, Broadcast), built as review layouts in `site/app.js` (`IDEAS`, listed under `REVIEW`,
  none in the tile row) plus four review palettes. Rendered by `scripts/sheet_ideas.mjs` to
  `incoming/ideas-20261027/` (sheets at 240, 60 and 40 px, one 1080 render each, an index page), copied to
  `dist/ideas-20261027/` for the LAN. Ranked with reasons in `docs/sticker-ideas-20261027.md`; awaiting
  Garrett's picks. Two fix rounds from the 40 px sheets (type too small in Marquee and Broadcast, Orbit
  rings through the letters, a cursor mark on Pixel state, Dusk's clipped sun, Scoreboard dots too coarse).
  QA after a dist rebuild: events PASS, save PASS. Committed, not pushed: goes with the morning deploy.
- **Oct 27, night (Garrett's picks):** State: Postmark, Marquee, Pixel state, three more English stickers per state
  (153 new, 714 total); every existing bespoke file checksummed before and after, 0 changed. Colorways taken from
  what each state's English page doesn't show, balanced to 12 or 13 per colorway; `bespoke_check.py` now wants 14.
  Classic: Wave, Calendar (navy), Done (red), Dusk (red to navy) in red, white and blue, 42 per language (was 18),
  original 36 unchanged, first six = six layouts and six messages. Home showcase: NY to I voted early Marquee
  (forest), VA to Future voter Pixel state (navy): eight designs, eight colors. Pixel state map capped for tall
  states. QA: facts, Spanish, SEO, events, save PASS; cards PASS (80,640 per engine, 0 outside the safe circle);
  no horizontal overflow on home or Texas at 390 and 1280. Committed, not pushed: goes with the Oct 28 morning deploy.
- **Oct 27, 7:57 p.m.:** Garrett said push. Third push of the day; run 40612079150 succeeded, live QA PASS,
  IndexNow 200 (108 URLs). Live home shows the four new classic layouts; state pages show Postmark, Marquee and
  Pixel state.
- **Oct 28, 6:15 a.m.:** dated check. The 6:05 cron had not started (it ran at 11:25 a.m. yesterday), so pushed
  `047cb58` and dispatched run 40678514466 (success, 3m36s). Live Louisiana and Pennsylvania: the summary, banner
  and FAQ say early voting ended October 27, the share image is `og/ended/<state>.jpg` (200), and the gallery keeps
  its I voted early stickers for those who voted early. First push of the day.
- **Oct 28, 6:30 a.m.:** BigQuery: no `analytics_559738937` dataset yet in `seo-command-ctr` (14 datasets, none
  analytics; read with the service account, gcloud user auth has lapsed). GA4 creates it on the first daily export,
  which can take up to a day after the Oct 27 link. Re-check this afternoon, then read the dataset's expiry.

### October 28, 8:25 a.m.: Stale-Date Sweep

No open item before the afternoon BigQuery check, so a sweep of today's local build. `qa_facts.py`: PASS,
51 states, 102 pages. `qa_stale.py 2026-10-28 dist`: 27 sentences, all ranges still open (start date past,
end date ahead) or past tense (Vermont "mailed them by October 1"); no deadline that has passed is still
offered. Maine's in-person absentee window ends October 29, Utah and Texas October 30; the `early_over`
switch handles each on the next morning's build. Backlog: countdown hardening was already built (the
browser recomputes "N days away" every minute), marked done. No push.

### October 28, 9:25 a.m.: Link Check, Two Dead Official Links Fixed

BigQuery: `analytics_559738937` still not created; re-check this afternoon. New `scripts/qa_links.py` fetched
all 649 external links on the 102 state pages. Most non-200s are 403s and TLS refusals from government
sites that block scripts (Ohio, Missouri, Iowa, New York and others); not dead. Tennessee's DNS error was
transient (200 on retry). Two were real 404s, confirmed with curl:
- **Louisiana** official link `sos.la.gov/ElectionsAndVoting/Pages/default.aspx` (the site moved): now
  `https://www.sos.la.gov/elections-voting` ("Elections and Voting", 200).
- **South Dakota** source, the 2026 election calendar PDF: the state dropped a folder from the path. Same
  PDF at `.../general-information/2026%20Election%20Assets/Election%20Information/updated2026electioncalendar.pdf`
  (200, still lists absentee voting from September 18).
Rebuilt; `qa_facts`, `qa_es`, `qa_seo --no-legacy` PASS. Second push of the day.

### October 28, 10:35 a.m.: Lookup Links Opened in Chrome

The 403s from the 9:25 link check, re-read in Chrome for the lookups voters use on Election Day. Load fine:
Arizona BallotTrax, Rhode Island Voter Information Center, New York Poll Site Search, Ohio voter lookup,
ballot tracking and early voting locations, Iowa Voting at the Polls, Alaska Polling Place Locations, New
Hampshire Absentee Ballots. Missouri (voter portal, How to Vote) shows a Cloudflare security check that a
person passes; not bypassed, link kept. **Arkansas and Nebraska VoterView** (`voterview.ar-nova.org/voterview`,
`votercheck.necvr.ne.gov/voterview/`) refuse the TLS connection from here, from Chrome and from WebFetch; no
outage reported in a search. Both Secretary of State sites still link those exact addresses, so ours are
right. Likely a block on VPN or data-center traffic, or a short outage. Re-check October 29; if still down,
look for a county or state fallback to name beside it. No change, no push.


### October 28, 11:40 a.m.: BigQuery Re-Check, Backlog Tidy

- **BigQuery:** `analytics_559738937` still returns Not found in `seo-command-ctr`. The link was made
  October 27, and Google says the first export can take up to 24 hours or longer. Re-check tonight or at the
  Oct 29 morning check. The billing question for Garrett stands.
- **Backlog:** every maker idea from the October 22 history research has shipped (First time voter, Future
  voter, the 48 px profile preview, Copy image description, Cut out the circle, the 4:5 Post size), and so
  has Tilt. `docs/backlog.md` now says so, so a later pass doesn't rebuild them.
- No site change, no push. Every loop table item is done; what's left are the dated checks (Oct 29 Search
  Console look and GA4 fields, the daily ended-state check, Nov 1, 2, 4, 5).

### October 28, 12:40 p.m.: Preview Builds for October 29 to 31

- Built with `VOTE_TODAY` set to Oct 29, 30 and 31 and read the titles. Maine keeps "Early Voting Oct 5–29"
  through its last day and turns to "I Voted Early Stickers: Vote Nov 3" on Oct 30; Utah and Texas keep
  their Oct 30 window through the 30th and turn on Oct 31. Spanish Maine and Texas turn on the same days.
- Rebuilt for today afterward; facts and Spanish QA pass. No push.
- Next: the Oct 29 dated checks (Search Console look for the Oct 24 pages, first GA4 read, AR and NE
  VoterView, BigQuery).
