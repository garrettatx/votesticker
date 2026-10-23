# Vote Sticker: Resume Here (Updated October 23, 2026)

Election Day is Tuesday, November 3. Everything is committed and pushed. **The October 22 late
session (iPhone save fix, three new designs, small-phone finder fix) is committed to main but not
deployed by hand**. It passed every QA locally. The daily Action (`.github/workflows/daily-build.yml`)
deploys main at 6:05 a.m. CDT, so it goes live on its own October 24 unless deployed sooner or
reverted. Start there.

## Where Things Stand

- **All 51 state guides are live** (facts files in `build/state-facts/`, every one `"live": true`).
  Open fact calls that stayed off the pages are listed in `docs/backlog.md` (SC, NJ, MI, KS, AR, MT,
  WY, WV, VT, NH; early windows for VT, ME, MT, ND, SD).
- **Sticker history page:** https://votesticker.org/history/ and https://votesticker.org/es/history/
  - Words: `build/history.json` (English and Spanish). Evidence: `build/history-facts.json` (source,
    quote, check date, status). `build/history_text.py` writes `site/history-data.php` and refuses any
    claim whose fact isn't `verified`; `--draft` previews locally, and `build/build_static.py` refuses
    a draft. Renderer: `site/history.php`.
  - Links into it: home "Why This Exists", the 29 state pages with a sticker story (link text fits
    each story), the footer, and one line after a sticker download.
  - Plan and the history writing rules: `docs/sticker-history-plan.md`. Sitewide writing rules from
    the reviews: `docs/brand-brief.md` (last section).
- **Footer (every page, both languages):** "Vote Sticker is independent and nonpartisan…", link row
  (Make a sticker, State stickers, Sticker History, About, Share a source or correction), and the
  Garrett Digital line "…for businesses and nonprofits. Let's talk."
- **Home polish (Oct 22, evening, `4362e8a`):** shorter hero and lead ("Make an I voted sticker or
  share your state's voting dates…"), "Type your state" picker with "Free, no account.", no copy-link
  button on the home hero, Election Day countdown ("Election Day: Tuesday, Nov. 3" plus a muted gold
  "12 days away" pill) on the home page and every state page (`vote_countdown()` in
  `site/index.php`; build sets the count, a footer script recounts by the visitor's date and hides
  it after Nov 3), plainer How It Works, home FAQ reordered (profile picture first). Kept on purpose:
  the H1, no hero caption, no state-preview swap, the trust band.
- **Saving on iPhone (Oct 22, late):** ready-made stickers went to Files instead of Photos on iOS 27
  (a plain `download` link). Both ready-mades and the maker now use the share sheet (Save Image) on
  iOS and fall back to a full-screen press-and-hold view, never `download`. `scripts/qa_save.mjs`
  checks every branch. The share sheet itself can't run headless, so check once on a real iPhone.
- **Home finder at 320 to 374 px (Oct 22, late):** the box was 118 px wide beside its button on the
  smallest phones; the button now goes under it. The earlier "card QA passed" report was wrong: the
  run had printed FAILED for this, and it was live from the home polish until this deploy.
- **Designs (Oct 22, late):** Shadow, Ring and Half replaced Poster and Stamp; history and the rejected
  sketches are in `docs/backlog.md` under Sticker Designs.
- **Resist Now:** three Learn-guide links on state pages (registration, voter ID, mail voting);
  rule in `docs/brand-brief.md`.
- **IndexNow:** all 108 URLs submitted October 22 (200).
- **Traffic (Ahrefs Web Analytics, Oct 20 to 22):** 213 visitors, 120 from Google, mostly to state
  pages (TX, NC, NY first). GSC isn't connected to the Ahrefs project; connect it to see queries.

## Open Items, in Order

1. **October 23 evening state:** everything through `c3892ab` is on main, not deployed by hand (the
   manual deploy was blocked by the permission classifier; Garrett can run it or let the 6:05 a.m.
   October 24 build ship it, which also sends IndexNow for every changed URL). Shipping with it: GA4
   events and /go/ share links, Maine/Nebraska/South Carolina dates and hours, sticker answers for TX,
   NC, SC, MO and WA (`sticker.early`, `sticker.mail` in the facts files), and the early-voting-ended
   switch (`vote_close_early()`; LA and PA flip October 28). Plan: `docs/content-plan-20261023.md`;
   open research questions: `docs/perplexity-prompts.md` Round 3.
2. **Deploy the October 22 late work** (Garrett's OK, or the October 24 daily build does it). Build, deploy, live QA (all five
   scripts, including `qa_save.mjs` without `--base`), IndexNow for all 108 URLs (the maker and
   finder are on every state page), then Garrett checks on his iPhone: tap a Texas ready-made, Save
   Image, confirm it lands in Photos. Local QA on October 23: cards PASS (53,760 per browser, page
   shots 320 to 1440), save PASS, SEO, Spanish and facts PASS.
3. **Indexing follow-up.** On October 23, 11 of the 12 unindexed URLs had Request Indexing submitted
   (Colorado, North Dakota, and Spanish Arkansas, Connecticut, Georgia, Kentucky, Louisiana, Vermont,
   Washington, West Virginia, Wisconsin). The daily quota ran out before **`/es/wyoming/`**: submit it
   unless the IndexNow run or a recrawl has already picked it up. Around October 27, re-run
   `python3 -u scripts/gsc_inspect.py` (all 108 URLs, about 10 minutes) to confirm.
   Priorities and research topics: `docs/search-console-priorities-20261023.html`; Garrett is
   running Perplexity research on those topics, then plan content per page (Texas, home, and the
   high-impression, low-click pages first).
4. **Analytics live check (October 24, after the build).** On the live site: save a ready-made and a
   maker sticker, copy post text, open a `/go/texas/plan` link; confirm GA4 Realtime shows
   `sticker_save`, `post_text_copy` and a `sticker_post` source. Then Garrett's GA4 and Search Console
   admin items in `docs/backlog.md` under Analytics. Spec: `docs/analytics.md`; QA `scripts/qa_events.mjs`;
   archive `scripts/archive_search.py` (Sunday weekly job). Research digest for the content plan:
   `docs/research/perplexity-20261023.md`.
5. **"Early voting has ended" copy** before each state's window closes (Texas ends Oct 30). Plan
   Section 5.1 in `docs/content-strategy-state-pages.md`; the daily build makes date logic safe.
6. **After Nov 3:** the countdown hides itself, but the home hero copy and the H1 still talk about
   voting; plan the post-election version.
7. **Maker ideas** in `docs/backlog.md`: "First time voter" message, 48 px profile preview, copy alt
   text, cut-out PNG, 1080 x 1350. Design follow-ons (tilt control, Stack as a fix to Big type,
   cover photo) are under Sticker Designs in the same file.
8. **History page, later:** Alaska and Maine ranked-choice Resist Now link; more state and county
   stories (blank states are fine as "No featured story yet"); records requests and interviews
   (Boudreau, Rowan, Michigan, Ulster) need a person to send them.
9. **After the election:** privacy and accessibility section on About; "Submit a sticker" needs a
   rights plan.

## How To Build and Ship

```
cd clients/hobby-sites/votesticker
python3 -I build/facts_text.py && python3 build/history_text.py   # compile facts and history
python3 -I build/build_static.py                                   # render dist/
python3 -m http.server 8792 -d dist                                # local preview
python3 -I scripts/qa_seo.py --base http://127.0.0.1:8792/ --no-legacy
python3 -I scripts/qa_es.py --base http://127.0.0.1:8792/
python3 -I scripts/qa_facts.py --base http://127.0.0.1:8792/
node scripts/qa_cards.mjs http://127.0.0.1:8792/                   # slow; run in the background
node scripts/qa_save.mjs --base http://127.0.0.1:8792/             # saving on iPhone, Android, desktop
node scripts/qa_events.mjs --base http://127.0.0.1:8792/           # GA4 events, share links, no analytics in QA
wrangler pages deploy dist --project-name votesticker --branch main --commit-dirty=true
```

Then live QA (same scripts without `--base`), IndexNow for changed URLs, commit with explicit paths,
`git pull --rebase -X theirs` (the daily Action commits `build/lastmod.json`), push. The PHP preview
server is `php -S 127.0.0.1:8791 -t site`.

## Standing Rules

- Anything committed to main is live by the next 6:05 a.m. CDT daily build. Treat a push to main as a
  scheduled deploy.

- No DNS or Cloudflare change without a summary and Garrett's OK. No Firecrawl unless he asks.
- Max two agents; one when his usage is low.
- No em dashes; Title Case headings; humanizer pass on prose; every file named gets a full link.
- Spanish: tú register, English state names, `docs/spanish-style.md`; add every new English UI
  string to `site/i18n/es.php` (run `qa_es.py`).
