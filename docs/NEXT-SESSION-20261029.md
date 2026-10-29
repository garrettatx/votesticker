# Vote Sticker: Resume Here (October 29, 2026)

Election Day is Tuesday, November 3. Supersedes `NEXT-SESSION-20261027.md`. Three pushes went out October 28
(the last one, Kentucky and the ten lowest-impression states plus the iPhone save hint, is live and checked).
**30 commits are local and unpushed**, the branch is one daily-build commit behind origin, and the working tree
is clean. All QA passed on the last build (facts, Spanish, SEO; events and save earlier that evening).

## First Thing on October 29
1. `git pull --rebase` (one daily-build commit behind), rebuild, run the full QA set (`qa_facts`, `qa_es`,
   `qa_seo --no-legacy` with `--base http://127.0.0.1:8792/`; `qa_events.mjs`, `qa_save.mjs`; `qa_stale.py
   2026-10-29 dist`, which should match the known set of open-window start dates).
2. Push, dispatch `daily-build.yml`, watch it, spot-check live (a batch 2 to 4 state, the home page's new
   sections, Texas and Michigan sticker stories), IndexNow for changed pages.
3. Dated checks from `docs/LOOP-election-prep.md`: Oct 29 Search Console look for the Oct 24 pages, first read of
   the GA4 v2 fields (the Oct 28 BigQuery table lands Oct 29 or 30), Arkansas and Nebraska VoterView, Maine's
   ended state on the Oct 30 build.

## What the 30 Commits Contain
- 30 more state pages re-verified against official sources from Perplexity leads (batches 2 to 4: IA ID MS NE OK
  DC IN ME NV CT, MO HI KS AR GA WI AL VA PA MA, TN NM IL AZ MI OH CO MN SC UT), with FAQ answers and sources.
- Sticker stories: Kansas (Sedgwick, described), Texas (El Paso plus Williamson early-voting stickers), Michigan
  (all nine 2026 winners). Florida mail-voter sticker (Sarasota). CA and OR lose an unsupported line.
- Template: "Pick an" before vowel states; mail FAQ asks how to track, not whether counted; Key Dates topic
  labels; "Check your registration status"; the not-official line under every state gallery (Garrett's OK).
- Home: Official Digital Stickers From Election Offices, I Voted Sticker Contests with 2026 winners, Sticker
  stories from 44 states, sourced history teaser (Boudreau, Intab, Smithsonian), turnout FAQ with both studies'
  limits, mail-voter sticker answer.

## Open Work, in Order
1. **Batch 5, the 11 strongest pages** (AK NJ LA MD WA OR NC NY FL CA TX): Perplexity reads saved as
   `docs/research/low-pages-20261028/<slug>-b5.md`. Verification was stopped before any file changed; restart it
   with two agents (AK NJ LA MD WA OR; NC NY FL CA TX). Leads worth checking first: NC ID cure date under SL
   2026-60, TX Nov 9 mail cure and Mountain Time counties, MD print-at-home ballot through Oct 30, OR and WA
   drop-box advice this week, AK overseas receipt date.
2. **"Still need to vote? / Already voted?" panel** under the sticker downloads, from verified facts. Every b5
   answer led with it.
3. **Michigan page order**: official 2026 designs right after our gallery; trim repeated voting facts.
4. Nov 1, 2, 4, 5 dated items in `docs/LOOP-election-prep.md`.

## Waiting on Garrett
- "I Voted Early Stickers" in state titles while early voting is open (GSC: ~400 impressions a week on "i voted
  early sticker" landing on state pages, ~5% clicks).
- Optional disclosure: our stickers aren't proof of voting; official designs belong to their creators.

## Where Things Are
- Method, batches and findings: `docs/LOOP-visibility.md` (October 28 entries).
- Research: `docs/research/low-pages-20261028/` (prompt template, per-state reads, page copy, pulse,
  home-topics, Clearscope reads for national, Dallas, Michigan and Kansas, Perplexity's interpretation).
- FAQ edits go through `build/faq_text.py` plus `docs/faq-sources.json`, then `python3 -I build/faq_text.py`;
  facts through `build/state-facts/<abbr>.json`, then `python3 -I build/facts_text.py`.
- LAN preview: http://192.168.1.252:8793/ (task bq3f10d7v serves `dist/`).

## Not Ours to Commit
Untracked and owned by other sessions: `data/`, `docs/early-sticker-research-2026-10-23.md`,
`docs/early-voting-research-2026-10-23.md`, `docs/sticker-roundup-competitive-research-2026-10-23.md`.
