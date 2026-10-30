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

### October 29, Night: Loop 1, Perplexity Election-Week Research
Goal (Garrett): what a voter landing Nov 1 to 4 needs that our pages and competitors don't give, for the home
page plus TX, NC, NY, FL and CA. Perplexity Deep Research in Chrome, one prompt per target; reads and prompts in
`docs/research/election-week-20261029/`. The first three long prompts stalled in Deep Research at about 6 minutes
with no text; short prompts (`short-prompt-template.txt`) finished in 7 to 9 minutes each.

Shipped (verified on the official page, EN and ES):
- Texas: "How do I track my mail ballot?" (votetexas.gov tracker page, read in Chrome; it was missing); "Is it too
  late to mail my ballot?" (SOS calendar: postmarked by 7 p.m. Nov 3 and received by 5 p.m. Nov 4, or hand it in
  yourself on Election Day with ID); "I voted by mail. How do I get a sticker?" (El Paso County's digital "I Voted"
  sticker for mail voters, epcountyvotestx.gov); provisional answer adds the regular-ballot route when your name
  isn't on the list (registration certificate plus photo ID, votetexas.gov/faq).
- North Carolina and Florida: "Is it too late to mail my ballot?" (receipt, not postmark: NC 7:30 p.m., hand it
  to the county board office, not a polling place; FL 7 p.m., drop boxes at Supervisor offices until then).
- Florida: provisional answer adds that a voter who voted provisionally only for lack of ID needs no more proof if
  otherwise eligible (dos.fl.gov voting FAQ, question 8). Our answer implied everyone had to bring proof by Nov 5.
- Home: three more official digital stickers: Oregon Secretary of State (I Voted, I Voted By Mail), Grand Rapids
  (nine languages, Arabic to Kinyarwanda), Ann Arbor (eight languages).

Verified but not shipped (needs a template change): more county sticker stories per state. The page has one
`sticker.tradition` slot (plus `early` and `mail`), already filled on all five pages, and each fact carries one
source. Deep Research found 25+ county stories; these I read on the office's own page Oct 29:
- TX: Guadalupe County (first contest; Julianna's design on the 2025-2026 stickers); Gaines County (first high
  school contest, stickers at all early voting and Election Day vote centers in 2026; winner named only in the image).
- NC: Forsyth County (Paloma, The Arts Based School: hands spelling "Vote" in sign language, the stickers voters
  get "this Fall"); Jackson County (Naomi's design at all county voting sites while supplies last).
- FL: Seminole County (first student contest, printed for polling places in the 2026 cycle). Collier (Presley,
  North Naples Middle School) and Pinellas ("I Voted...By Mail" digital sticker) block curl; read in Chrome first.
- NY: Genesee (seven winners by school; page blocks curl), Madison (contest rules name Nov 3, 2026), Warren, Dutchess.
- CA: Orange County (peel-off "I Voted" sticker in the Nov 3 vote-by-mail packet, read in the PDF); Foster City
  (stickers at its vote centers Oct 31 to Nov 3).
- MI (home lead): Grand Rapids and Ann Arbor multilingual digital stickers also fit Michigan's page.
Proposal: an optional `sticker.more` list (en, es, src, quote, checked per entry) rendered as a short "More county
stickers" list under the story, checked by the same QA. About 20 lines in facts_text.py and index.php. Not built;
Garrett's call. Highest-value entries first: Orange County (CA's biggest mail-voter sticker after San Diego),
Forsyth (sign language), Pinellas digital (mail voters).

Rejected: Suffolk County NY "no stickers" and Nassau early-voting stickers (Newsday only); Humboldt CA
multilingual sticker details (news only; county page confirms the program through 2027); Lassen CA (poster doesn't
name Nov 3); Butte CA winner (news only); Lavaca County TX winners (only an agenda item and local news); Chatham County NC winner (news only);
Denton County design talk and a Dallas sticker shortage (news and Reddit); Travis County bilingual sticker (2022);
Jefferson County CO mailed stickers (Colorado page already has its statewide digital sticker story; CO not a target
this loop); a 2025 sticker study (measures intention to vote, not turnout); EAC glossary "you can take photos"
sample sentence (translated dialogue, not a rule).

Open Fact Calls: none new. Note for later: the TX FAQ page still carries stale lines (a "2023" postmark, "Primary
Election"); we cite the dated SOS calendar and advisories instead.

Research files: `docs/research/election-week-20261029/` (home, texas, north-carolina, new-york, florida, california
.md, prompts). Election-week facts in the reads for NY and CA were already on our pages (affidavit ballot, line at
closing, hand postmark, Nov 10 receipt). Next: Garrett's call on `sticker.more`; if yes, Chrome reads for the
curl-blocked pages, then build. Re-read these five pages' queries Nov 12.

Push (9:49 p.m.): 11 commits (7 waiting plus 4 from this loop). Full QA passed first (facts, Spanish, SEO, events,
save; stale check 28 known open-window lines). Run 40993926526 succeeded, IndexNow 200. Live: Texas shows the
tracker, too-late-to-mail, mail-voter sticker and regular-ballot lines; home shows Oregon, Grand Rapids and Ann
Arbor; Spanish Florida shows the no-ID provisional line.
