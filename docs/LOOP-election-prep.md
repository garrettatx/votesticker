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
| 3 | Post-election checklist (`docs/post-election-release.md`) checked against the build; build what's missing (post-election home FAQ) | FAQ done Oct 25; rest of checklist next |
| 4 | "Build and QA, no deploy" input on the daily workflow, so the Nov 4 run can be tested once ahead | Built Oct 25 |
| 5 | "First time voter" message in the maker (English and Spanish) | Done Oct 25 |
| 6 | Ballot trackers: confirm IN; look for IL, MO, WY | |
| 7 | Open fact calls that touch the Nov 2 banner (NJ, MI, KS, VT, WY, MT) | |
| 8 | Mail-state ribbon and date line field (WA "BY 8 P.M. NOV 3") | |

## Dated Checks

| Date | Check |
|---|---|
| Daily Oct 26 to Nov 1 | After the 6:05 a.m. build (5:05 from Nov 2): states whose early voting ended show the ended title, gallery and share image live; IndexNow for changed pages |
| Oct 29 | First impact look: Search Console impressions for the 21 pages updated Oct 24, from each page's recrawl date |
| Nov 1 (Sunday) | Index snapshot, `archive_search.py --index` |
| Nov 2 | Banner live: hours, lookups, mail return lines; the 15 states still voting early that day keep their early-voting lines |
| Nov 4 | 06:30 UTC run deployed post-election mode; spot-check EN and ES; rerun from Actions with `2026-11-04` if not |
| Nov 5 | Remove the `30 6 4 11 *` cron; log the release in `docs/change-log.csv` |

## After the Election

| # | Item | Status |
|---|---|---|
| 9 | Between-elections mode built behind a phase flag, ready for the Dec 1 review | |
| 10 | Sticker collection and 2026 archive (`docs/sticker-archive-spec.md`): data first | |
| 11 | Descriptive image filenames with 301s from the old URLs | |
| 12 | Snohomish County as a second Washington sticker source | |
| 13 | Privacy and accessibility section on About | |

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
