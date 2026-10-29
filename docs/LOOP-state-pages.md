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

## Status (October 28, 9 p.m.)
| States | Status |
|---|---|
| KY plus ND NH WY SD WV DE VT MT RI | Live Oct 28 |
| IA ID MS NE OK DC IN ME NV CT; MO HI KS AR GA WI AL VA PA MA; TN NM IL AZ MI OH CO MN SC UT | Ready to ship (committed); KS TX MI sticker stories, FL mail sticker; home sections; titles; template fixes |
| AK NJ LA MD WA OR | Almost ready: research saved (`*-b5.md`), checking agent running Oct 28 night |
| NC NY FL CA TX | Almost ready: research saved (`*-b5.md`), checking agent running Oct 28 night |

## Next After the 11
- "Still need to vote? / Already voted?" panel under the sticker downloads (every b5 read led with it).
- Michigan page order: official 2026 designs right after our gallery.
- Re-read the ten lowest pages' queries Nov 11, 14 days after recrawl.

## Log

### October 28, 9 p.m.
Loop started. Early-voting titles and the proof-of-voting line committed (Garrett's OK). 32 commits wait for
the Oct 29 6:30 a.m. push. Batch 5 checking restarted with two agents.
