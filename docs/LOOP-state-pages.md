# Loop: Ship Updated State Pages

Started October 28, 2026, at Garrett's request: get updated state pages live as soon as they're verified, and
keep the next batch ready or nearly ready. Works alongside `LOOP-election-prep.md` (dated checks win) and
`LOOP-visibility.md` (research and method).

## What "Ready" Means
- **Ready to ship:** facts edited in `build/state-facts/<abbr>.json` and verified against an official source;
  FAQ answers applied in `build/faq_text.py` with `docs/faq-sources.json`; `facts_text.py` and `faq_text.py`
  regenerated; full QA passes; committed.
- **Almost ready:** Perplexity read saved and a checking agent running, or checked with FAQ answers not yet
  applied.
- **Queued:** research not started.

## Each Pass
1. Dated checks in `LOOP-election-prep.md` first.
2. Anything an agent finished: apply its FAQ answers (`faqedit.py` pattern), regenerate, rebuild, QA, commit.
3. If ready commits are waiting and a push slot is open, push: `git pull --rebase`, full QA (`qa_facts`,
   `qa_es`, `qa_seo --no-legacy`, `qa_events.mjs`, `qa_save.mjs`, `qa_stale.py <today> dist`), `git push`,
   dispatch `daily-build.yml`, watch it, spot-check two changed pages live, IndexNow for the changed pages.
4. Keep two agents busy on the next almost-ready work (verification first, then research).
5. Log the pass below: what shipped, what's ready, what's next.

## Push Slots
At most three a day, at the end of a unit of work: about 6:30 a.m. (after the daily build), about 1 p.m., and
about 7 p.m. Central. Election Day week: Nov 2 build by 7 a.m.; Nov 4 post-election mode.

## Rules
Official sources only; Perplexity and news are leads. Students by first name and school or town. Plain,
7th-grade copy; no em dashes. Spanish in tú. Each state is its own case; carry over only proven patterns
(emergency absentee routes, photo vs. sharing, received vs. postmarked). Don't disturb what ranks on the
strongest pages: change only what's wrong, stale, missing or unclear.

## Status (October 28, late night)
| States | Status |
|---|---|
| All 50 states and DC | Live (Oct 28 and Oct 29 5:30 a.m. pushes) |

## Next
- Done Oct 29: the two-path panel (live 5:30 a.m.); the sticker story right after Key Dates on every state (committed, ships 1 p.m.).
- Re-read the ten lowest pages' queries Nov 11, 14 days after recrawl.

## Log

### October 28, 9 p.m.
Loop started. Early-voting titles and the proof-of-voting line committed (Garrett's OK). 32 commits wait for
the Oct 29 6:30 a.m. push. Batch 5 checking restarted with two agents.

### October 28, Late Night
Batch 5 (AK NJ LA MD WA OR; NC NY FL CA TX) verified and committed with FAQ answers; full QA passes (stale
check: 30 open-window start dates, down from 34). Every state page is now ready for the Oct 29 6:30 a.m. push.
Open from the agents: NJ cure deadline conflict (timeline vs poster), Camden County and Washington County MD
sticker pages need ALLOW_HOSTS, the "county and local election offices" link label is wrong for Louisiana
parishes, and Alaska shows a "fix a signature problem" link though it has no cure process.

### October 29, 12:30 a.m.
Built the "Still need to vote? / Already voted?" panel under the state gallery (Oct 29 to Nov 1; the Election
Day banner takes over Nov 2), from verified facts, EN and ES; checked on a phone-size screenshot. Building it
exposed a bug: app.js replaced the gallery heading on load, so the Oct 26 "I Voted and I Voted Early Stickers"
heading never showed in a browser (likely not to Google's renderer either). Fixed: the page's own state keeps
the server heading. Full QA passes. All of it ships in the 6:30 a.m. push.

### October 29, 5:30 a.m.: Push 1 of Oct 29
Pulled, regenerated, full QA passed (facts, Spanish, SEO, events, save, cards; stale check 30 known open-window
start dates). Pushed 40 commits; run 40858935741 succeeded; IndexNow 200. Live checks: Texas and Michigan titles
read "I Voted and I Voted Early Stickers", the two-path panel shows on Texas, Michigan and Oregon, Michigan's
2026 winners are up, home page sections live, Delaware shows the new Key Dates labels and the proof-of-voting
line. Every state page is now live with the re-verified facts.
Next: Oct 29 dated checks (Search Console look for the Oct 24 pages, GA4 v2 read when the Oct 28 table lands,
Arkansas and Nebraska VoterView), then Michigan page order and the open template items.

### October 29, 6:20 a.m.
Impact look for the Oct 24 pages logged in LOOP-election-prep (no lift yet; baseline). Sticker story moved up to
right after Key Dates on every state page, also after the election; QA passes. Ships in the 1 p.m. push. Next:
GA4 v2 read when the Oct 28 BigQuery table lands; the NJ cure deadline conflict; ALLOW_HOSTS for official county
sites on .com and .org (Camden NJ, Washington County MD, Johnson County KS, Montgomery County KS).

### October 29, 7:30 a.m.
Spanish review (agent, es-mx-localization rules with the site's tú and glossary): ~695 new strings checked, 65
fixed (Kansas entry counts read as a range, Montana "matasellos de esa hora", Minnesota "a una semana", Texas
in-person return calque, gendered wording made neutral). QA passes; ships in the 1 p.m. push. Open Spanish calls,
added to docs/spanish-style.md open questions: "casillas" (about 20 files) vs the glossary's "lugar de votación";
county clerk rendering in NM and IL; generic masculine "registrado"; "pareja de hecho", "notarizar", "asilo de
ancianos"; and the existing "Ya voté" vs "YO VOTÉ" question.
