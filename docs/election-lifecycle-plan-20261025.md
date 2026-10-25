# Election Lifecycle Plan, October 25, 2026

How the state pages, home page and maker change from now through the weeks after November 3. Revised
the same day after Garrett's review rounds: dated wording instead of "today", one extra post-election
build, stable sticker titles, and a separate link for each voter task.

## The Rule

Each action ends at its own deadline and gives way to the next useful one. The gallery, the maker and
the "I voted", "I voted early" and "I voted by mail" designs never go away. An official link stays on
the page after its invitation is retired.

The site states schedules; it never claims a live status. No "vote today", "polls close soon", "you
still have time", "last day to vote" or "open now" anywhere: banner, maker defaults, ready-made
images, post text. Dates are explicit ("Vote November 3"). Dated wording stays understandable if a
build is late; it is not a claim that voting is available at that moment. The site never guesses the
visitor's location from the browser or IP.

## Modes

Internal names describe the election phase, not the visitor: the site knows the date, not whether
someone voted.

| Mode | Starts | State page leads with | Maker default |
|---|---|---|---|
| `early-open` | Now, until each state's end date | Dates, where to vote early | Vote early |
| `early-over` | Day after the state's last early day (built Oct 23) | Election Day plan, polling place lookup | Vote Nov 3 |
| `election-day` | November 2 build | Dated Election Day banner (below) | Vote Nov 3 |
| `post-election` | Transition build after the last scheduled cutoff (below) | "Voted? Get your sticker." Gallery first | I voted |
| `between-elections` | Reviewed state by state by December 1 | Evergreen intro, gallery, official upcoming-elections link | I voted |

`between-elections` is a review target, not a cutoff. A state keeps any verified follow-up that still
applies; nothing is assumed finished because of the date. No "next election" countdown.

## Build Timing

- The daily build stays at 11:05 UTC. That is 6:05 a.m. Central through November 1 and 5:05 a.m.
  Central after Daylight Saving Time ends; document it as UTC, since GitHub cron runs in UTC.
- The Election Day banner goes out with the November 2 build, dated, so no midnight build is needed and
  nothing says "tomorrow" or "today".
- One extra build switches every page to `post-election` after the latest scheduled cutoff across the
  states, taken from the reviewed poll hours (Alaska and Hawaii close last). It runs with
  `VOTE_TODAY=2026-11-04` so the mode follows the election date, not the server clock. Its UTC time
  goes in the workflow file once the hours are verified. The 11:05 UTC November 4 daily build is the
  second chance and the check.
- Until the transition build runs, eastern pages keep the dated Election Day banner for a few hours
  after their polls close. That is acceptable because the banner states the schedule and never invites
  anyone to go vote now.
- Failure: GitHub emails Garrett on a failed run. The manual fix, from the repo:
  `VOTE_TODAY=2026-11-04 python3 -I build/build_static.py` then
  `wrangler pages deploy dist --project-name votesticker --branch main --commit-dirty=true`.
- Deferred: state-by-state closing builds, mixed-closing banners, browser-side expiry.

## Election Day Banner

One component, in the page flow above the state heading, not sticky, no countdown, no red, no
exclamation marks. A `<section>` with a real heading, no `role="alert"`.

In-person states:

> NOVEMBER 3, 2026 · ELECTION DAY
> Find your Election Day voting location and hours.
> Scheduled voting hours: [verified hours]. [Time note]
> [Find my voting location] · Already voted? Get your sticker.

Mail states:

> NOVEMBER 3, 2026 · BALLOT RETURN DEADLINE
> Check your official ballot return instructions.
> [Verified return rule; received-by and postmarked-by stay separate, never merged.]
> [Check return instructions] · Already returned it? Track your ballot.

Time note, one of:

| Situation | Line |
|---|---|
| Same clock hours across two time zones | "Hours are in your voting location's local time." |
| Hours set locally (towns, counties) | "Check your location's official hours before you go." |
| Hours not verified | No hours line; link straight to the official lookup |

Home page: "November 3, 2026 · Election Day. Find your state's voting locations, hours and ballot
return rules." pointing at the existing state picker, no second selector.

## The November 2 Build

Publishes the Election Day version ahead of time. Same layout; it changes the banner, the order of the
voting section, the featured stickers, the post text and the descriptions.

- **States still voting early on November 2** (15 end that day in `states.js`: AK, AR, IA, IL, IN, KS, MN,
  MO, MT, ND, NE, RI, SD, VT, WY): the banner appears, and the page keeps its
  early voting answer for November 2. Nothing says early voting has ended until the November 3 build.
- **Voting section order:** Election Day location and hours first; any early voting still open; mail
  return rules where they apply. Request deadlines already past drop out of the recommendations. A
  finished early window is labeled "Ended" with its dates. Individual past deadlines are labeled; the
  table as a whole is not "past dates" yet.
- **Featured stickers:** I voted, I voted early, I voted by mail where offered, then a dated
  "Vote Nov 3" reminder. "Vote early" is never the default once the state's window has closed.
- **Post text:** planning version "I'm making my voting plan for November 3. Check where to vote, hours
  and ballot return rules for [State]: [link]"; already voted version "I voted. Get a sticker or find
  official voting information for [State]: [link]". None of the banned relative phrases. Sharing never
  implies someone voted.
- **Descriptions:** "Download free [State] voting stickers, make your own, and find official
  information for the November 3, 2026 election."
- **Home page:** the dated banner pointing at the state picker; state cards drop "early voting open"
  badges for states whose window has closed and show neutral labels (Election Day information, Ballot
  return rules). No national poll hours summary.
- **Images:** no regeneration unless the content needs it; expired reminders leave featured spots; the
  share image must not contradict the page.
- **Prepared, not switched on:** the `post-election` build and its command, previewed with
  `VOTE_TODAY=2026-11-04`, switching gallery, maker, post text and dates together.

Test cases for the November 2 preview (`VOTE_TODAY=2026-11-02`), English and Spanish: Texas (early voting
ended), Iowa (early voting through November 2), Washington (mail), Indiana or Florida (two time zones),
New Hampshire (towns set hours), Alabama (no early voting open to everyone), and a state with no
verified tracker.

## Links, One Per Task

`where.locator_url` is a polling place lookup in some states and a general portal in others. The
banner needs the right one for the task:

- `election_day_url`: where to vote on Election Day
- `ballot_return_url`: drop boxes or return instructions (mail states)
- `ballot_tracker_url`: ballot status, only where the state has one

Each comes from an official page with a quote, like every other fact. Loop batch 3 is collecting hours
and trackers now.

## Titles

Titles follow what the page offers, not a voting window. "I voted early sticker" is a search that
outlives early voting.

| Mode | Title pattern |
|---|---|
| `early-open` | Unchanged (indexed, avoid churn eight days out) |
| `early-over`, `election-day` | "[State] I Voted and I Voted Early Stickers: Vote Nov 3" |
| `post-election` | "[State] I Voted and I Voted Early Stickers" |
| Mail states, after the deadline | "[State] I Voted and I Voted by Mail Stickers" |

Dates move to the description and the page. 65 characters stays an internal preference; Google has no
fixed limit. States with no early voting open to everyone (Alabama, Mississippi, New Hampshire) use
"[State] I Voted Stickers".

## Post-Election Mode

- Intro: "Voted? Get your sticker." Gallery right after it; "I voted" selected.
- Post text: already-voted versions only.
- Ballot tracking link only where `ballot_tracker_url` is verified.
- Dates table below the gallery under "November 3, 2026 election (past dates)".
- Ready-made images with deadline text leave featured positions; their URLs stay.

## Sticker Answers

Only verified local answers appear. No national boilerplate on state pages ("many offices hand out
stickers"); that background belongs on the home page, history page or collection. A state with no
answer shows nothing rather than two near-identical "we couldn't confirm" answers.

The facts check is the publication gate, not a research summary's confidence. Kept apart:

- mail instructions mention a sticker ≠ every packet includes one
- available at polling places ≠ available at early voting
- statewide contest winner ≠ handed out in every county
- one county's practice ≠ the state's practice

## Acceptance Check

For each mode, using `VOTE_TODAY`, in English and Spanish: heading, banner, official link, gallery
order, maker default, download names, post text, title, description and share image agree. Spot check
an in-person state, a mail state, a state spanning time zones, one where towns set hours, and a state
with no early voting open to everyone. Then run the QA scripts against the local build
(`qa_es.py --base http://127.0.0.1:8792/`; without `--base` it checks the live site).

## Measurement

Observational, not a controlled test: states differ in schedule, queries and competition, and demand
changes daily toward November 3. Segment by state, query group, device and mode. Count from the
recrawl date, not the deploy date (URL Inspection `lastCrawl`, archived weekly). What matters, in order:

1. Wrong or expired instructions on the site: zero.
2. Clicks to official tools.
3. Sticker saves, post text copies, shares (GA4 events).
4. Search clicks by query group; click rate and position as context.

Downloads and clicks are never reported as votes.

## After the Election

Sticker collection and archive: `docs/sticker-archive-spec.md`. Descriptive image filenames are
optional even then; only with redirects from the old URLs.

## Image Search, October 25

The stickers are plain `<img>` tags with alt text, so Google can index them now: 125 image search
impressions October 20 to 25, mostly at positions 30 to 100, no clicks. The image sitemap (393 images
on 108 pages, each image shown on the page that lists it) adds discovery signals; indexing isn't
guaranteed. Treat the count as a QA inventory, not a target.

## Built October 25

- `vote_phase()` in `site/index.php`: `pre` to November 1, `eday` November 2 and 3, `post` from November 4,
  from `vote_today()` (`VOTE_TODAY` for previews). `window.VOTE_PHASE` tells the maker.
- Election Day banner (`vote_eday_banner`, `vote_eday_banner_hub`), from `election_day.hours` (48 states,
  official sources, loop batch 3), `dates.mail_return`, `where.locator_url` and `mail.track_url`.
- Post-election: titles and H1s, lead, info panel with tracker and official link, maker defaults and
  message list, post text, home copy, past-dates heading. The countdown loses Today/Tomorrow on
  November 2 and disappears November 4.
- Fixed: in-person states reverted to "Early voting runs Oct 19–30" after November 3; they stay `ended`.
- Galleries hide ready-mades whose message expired (`vote_file_current`, from the alt text). 27 states
  with few left got four maker-drawn ready-mades each (`scripts/render_readymades.mjs`).
- Share cards: `assets/og/ended/` (43), `assets/og/post/` (51), Spanish sets, `og-hub-post.jpg`;
  `build/og_cards.mjs` with `OG_SET=ended|post` against a build of that date.
- Workflow: `30 6 4 11 *` runs the build as November 4; a manual run takes a `vote_today` date.
- QA, run for today, November 2 and November 4: SEO, Spanish, facts, cards (51 of 51), GA4 events.
  `qa_seo.py` and `qa_es.py` check the live site unless given `--base http://127.0.0.1:8792/`.
