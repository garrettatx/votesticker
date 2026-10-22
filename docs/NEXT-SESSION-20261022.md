# Vote Sticker: Resume Here (October 22, 2026)

Election Day is Tuesday, November 3. Everything below is live, committed and pushed. Nothing is half
done.

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
- **Resist Now:** three Learn-guide links on state pages (registration, voter ID, mail voting);
  rule in `docs/brand-brief.md`.
- **IndexNow:** all 108 URLs submitted October 22 (200).
- **Traffic (Ahrefs Web Analytics, Oct 20 to 22):** 213 visitors, 120 from Google, mostly to state
  pages (TX, NC, NY first). GSC isn't connected to the Ahrefs project; connect it to see queries.

## Open Items, in Order

1. **"Early voting has ended" copy** before each state's window closes (Texas ends Oct 30). Plan
   Section 5.1 in `docs/content-strategy-state-pages.md`; the daily build makes date logic safe.
2. **After Nov 3:** the countdown hides itself, but the home hero copy and the H1 still talk about
   voting; plan the post-election version.
3. **Maker ideas** in `docs/backlog.md`: "First time voter" message, 48 px profile preview, copy alt
   text, cut-out PNG, 1080 x 1350.
4. **History page, later:** Alaska and Maine ranked-choice Resist Now link; more state and county
   stories (blank states are fine as "No featured story yet"); records requests and interviews
   (Boudreau, Rowan, Michigan, Ulster) need a person to send them.
5. **After the election:** privacy and accessibility section on About; "Submit a sticker" needs a
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
wrangler pages deploy dist --project-name votesticker --branch main --commit-dirty=true
```

Then live QA (same scripts without `--base`), IndexNow for changed URLs, commit with explicit paths,
`git pull --rebase -X theirs` (the daily Action commits `build/lastmod.json`), push. The PHP preview
server is `php -S 127.0.0.1:8791 -t site`.

## Standing Rules

- No DNS or Cloudflare change without a summary and Garrett's OK. No Firecrawl unless he asks.
- Max two agents; one when his usage is low.
- No em dashes; Title Case headings; humanizer pass on prose; every file named gets a full link.
- Spanish: tú register, English state names, `docs/spanish-style.md`; add every new English UI
  string to `site/i18n/es.php` (run `qa_es.py`).
