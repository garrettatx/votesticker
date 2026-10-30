# Loop: County Stickers and Last-Minute Mail Answers

Started October 30, 2026, at Garrett's request: give voters what competing pages don't, backed by evidence,
in the four days before Election Day. Works alongside `LOOP-election-prep.md` (dated checks win) and
`LOOP-state-pages.md` (push and QA routine). One agent at a time; log each pass at the bottom.

**Goal of every item:** a voter who lands on a state page Nov 1 to 3 finds the right sticker and the right
last-minute answer for their state, both from an official source.

## Rules
- The rules of `LOOP-state-pages.md` and `LOOP-election-prep.md` hold: official sources only, with the quote
  read on the page itself (curl, WebFetch or Chrome); Perplexity is a lead; plain 7th-grade copy; no em dashes;
  Title Case headings; students by first name and school or town; every EN change has its ES counterpart in tú
  per `docs/spanish-style.md`; nonpartisan; no Firecrawl.
- Research runs in Perplexity Deep Research in Chrome, short prompts with one question each. A tab with no
  answer text at about 6 minutes is stuck: close it and rerun. If a limit message appears, quote it exactly,
  wait and retry. Don't assume a cap (Garrett, Oct 30).
- Perplexity Computer: not used by default (Oct 30). Pro includes a weekly allowance whose size isn't published,
  and after that it spends paid credits. Use it only inside that allowance (check perplexity.ai/account/usage
  first), only for a task that the Space, regular search, Deep Research and our own browsing can't do, never
  with purchased credits, and log the reason.
- Perplexity Space "VoteSticker.org Research" (Oct 30): run research prompts inside it. Recent Library threads
  solely about voting or sticker topics get moved into it. Threads that mix in other topics or clients stay where
  they are. Move only; never delete or rename.
- Demand first: each item names the Search Console query or Ahrefs volume behind it.
- Push in batches as they're ready (Garrett, Oct 30), not on fixed slots. The repo is public on a standard
  runner, so Actions minutes are free and there's no daily cap. Each push is a meaningful batch, then IndexNow for that batch's changed EN and ES URLs, logged with the response code. Full QA before each (`qa_facts`, `qa_es`, `qa_seo --no-legacy` with
  `--base http://127.0.0.1:8792/`, `qa_events.mjs`, `qa_save.mjs`, `qa_cards.mjs`, `qa_stale.py <today> dist`).
- **Structure freeze Sunday Nov 1, 7 p.m.** After that, only fact fixes and content inside existing slots.

## Items

| # | Item | Evidence | Status |
|---|---|---|---|
| 0 | Push loop 2's waiting commits (history, post-election results answer), missed Oct 30 6:30 a.m. slot | 3 commits local | Done Oct 30 (run 41084630854) |
| 1 | **County Stickers**: a heading under the state's premade sticker gallery listing official county sticker stories (design, who made it, where voters get it, paper or digital), each with its own official source and quote. Optional `sticker.more` list in `build/state-facts/<abbr>.json`, rendered by `facts_text.py` and `site/index.php`, checked by `qa_facts`. Order by search demand (Garrett, Oct 30): counties with Search Console queries on our pages first, then counties with Ahrefs volume for "[county] i voted sticker", then strong verified entries with no measured demand. Demand-backed counties go first in each state's list, and the log shows each county's number. The dozen verified Oct 29 (LOOP-state-pages.md, loop 1 entry) are candidates, not a given | "[state] i voted sticker" is the top query on every state page; county contest pages outrank us; check GSC queries with county names and Ahrefs "[county] i voted sticker" first | Built Oct 30: 17 entries in 7 states (pass 1); more states next |
| 2 | **Last-minute mail answers for every state**: "Is it too late to mail my ballot?", "How do I track my mail ballot?", "My name isn't on the list" (provisional ballot). Done for TX, NC, FL Oct 29. Postmark vs. receipt, and where to hand it in, vary by state | Election-week searches Nov 1 to 3; a wrong generic answer can cost a vote | Pass 2 Oct 30: 23 states filled or fixed. Pass 3 Oct 30: name-not-on-the-list answers for MN MS NH (new) and TN SC NJ NE NV IA (stronger); trackers for IL (county list), IN and SD (statewide; pass 2 had them wrong as missing). Left: MO tracker (local only), ME and IN provisional lines |
| 3 | Open fact calls before the Nov 2 banner: NJ cure deadline (timeline vs. poster); ID affidavit routes AK AZ IA ND OK WY; ALLOW_HOSTS for official county sites on .com/.org (Camden NJ, Washington County MD, Johnson and Montgomery County KS) | Possible wrong answers on the busiest day | Pass 3 Oct 30: ID routes fixed (WY was wrong, ND incomplete; AK AZ IA OK right); ALLOW_HOSTS added; AR conflict gone (SOS FAQ now agrees); NJ left as is (Garrett) |
| 4 | Spanish wording decided by evidence: "lugar de votación" vs. "casilla"; "Ya voté" vs. "Yo voté" on stickers. What official U.S. Spanish election pages and stickers say, plus Spanish search volume (Ahrefs US, GSC). Recommendation to Garrett, then apply | Open questions in `docs/spanish-style.md` | Open |
| 5 | Michigan page order: official 2026 designs right after our gallery; trim repeated voting facts | Clearscope Michigan read | Open |
| 6 | After Nov 3: each state's official results page link and certification date where the state publishes one | Nov 4 to 10 searches | Nov 4 to 5 |
| 7 | History: notable designs prompt (not run Oct 29) | "i voted sticker history" 133 impressions at 8.7 | After Nov 3 |

## Log

### October 30: Pass 1, Push and County Stickers

**Item 0 (push 1 of Oct 30).** Pulled (up to date), rebuilt, full QA passed: facts, Spanish, SEO (local, --no-legacy),
events, save, cards; stale check 28 known open-window lines, unchanged. Pushed 3 commits; dispatched run 41084630854,
success; IndexNow 200 (in the run). Live: /history/ and /es/history/ show the 1920 Blewett tags, Camp Fire Girls and
Americus lines. Dated check: Maine's title reads "I Voted and I Voted Early Stickers: Vote Nov 3" (EN and ES) with "Early
voting (ended)"; Texas still shows "Oct 19–30" (turns tomorrow with Utah).

**Demand (item 1, step 1).** Search Console, Oct 2 to 29, query x page (315 visible rows, 2,438 impressions). Place names:
NYC 39 impressions over four queries ("nyc early voting stickers" 15, "nyc voting stickers" 14, "nyc voting sticker" 6, "nyc
i voted sticker" 4), New Orleans 5 (+1 "terrance osborne i voted sticker"), Chicago 1, Wake County 1, Santa Clara 3 to 6
(odd operator queries). Nothing else names a county or city. Ahrefs US volume: Ulster County 700 (+ "i voted sticker ulster
county" 20, "ulster i voted sticker" 20, 2022 10), NYC 30 + 30 ("i voted sticker nyc") + 10 + 10 + Halloween NYC 10 + 10,
San Francisco 10 + "sf" 10, Santa Clara County 10; Los Angeles, Harris, Maricopa, Cook, Chicago, Miami-Dade, King, Travis,
Dallas, Orange, Wake, Broward, Alameda, Ann Arbor, Philadelphia, Allegheny, Hillsborough, San Diego: 0; the other 38 counties
we asked about have no Ahrefs record. Read: the demand-backed places are already answered (Ulster is the NY sticker story,
Santa Clara the CA story, New Orleans the LA statewide Osborne story, NYC the NY early-voting answer), except NYC's 2026
specials and San Francisco. Everywhere else demand is near zero; the list was built anyway on the value case ("[state] i
voted sticker" is the top query on every state page, and these are the official local designs those searchers see on
county pages that outrank us).

**Research.** Perplexity project "VoteSticker.org Research" created (https://www.perplexity.ai/projects/45fabdec-4a18-4712-abb9-7c0334a78ddf),
instructions as Garrett gave them, Brain auto-update off, no files. Deep Research ran once (p1, 10:51 a.m.): "Finished 7m 22s"
with no answer text, also after reload. After that the Deep Research option showed a lock, and clicking it opened an upgrade
screen: "Upgrade for advanced research capabilities. Upgrade to Max for enhanced access to Perplexity deep research." Retried
about 8 minutes later in a fresh tab: still locked. So p1 (rerun), p2 (past 7 days) and p3 (big counties) ran in standard
Search inside the project (1m 20s to 1m 45s each, 127 to 219 sources). Reads and prompts in
`docs/research/county-stickers-20261030/`; every entry's quote and how it was read in `verification-20261030.md`.

**Shipped: 17 entries, 7 states** (`sticker.more`, "County I Voted Stickers in Texas" / "Stickers de los condados de Texas"
under the gallery, above the maker). Demand next to each; "0" means no Ahrefs record and no GSC query.
- CA (heading "County and City"): San Francisco (Ahrefs 10 + 10; every vote-by-mail packet has an "I Voted!" sticker, N26
  brochure PDF), Los Angeles County (0; Halloween sticker at 11-day Vote Centers Oct 31, official X post today), Orange County
  (0; peel-off sticker in the Nov 3 VBM packet, ocvote.gov PDF), Foster City (0; city's own sticker, City Hall and vote centers
  Oct 31 to Nov 3, fostercity.org).
- TX: Guadalupe County (0; Julianna's bluebonnet, Laila's Future Voter, guadalupetx.gov), Gaines County (0; Mattie, Seminole
  High School, co.gaines.tx.us).
- FL: Seminole (0; Arwen, Tuskawilla Middle, voteseminole.gov), Collier (0; Presley, North Naples Middle, colliervotes.gov),
  Palm Beach (0; Giahna, Independence Middle, votepalmbeach.gov).
- NC: Forsyth (0; Paloma, The Arts Based School, sign-language hands, county newsletter), Jackson (0; Naomi, 7, jcncelections.org).
- NY: Madison (0; Jeslyn of Morrisville, eagle and flag), Genesee (0; student winners at polling places), Tompkins (0; Leo,
  homeschooled, lake scene; Addie and Fiona, Lansing Middle, Future Voter), Dutchess (0; first contest winner at polling places).
- SC: Charleston County (0; Emmeline, Adalyn, Katherine; limited run during early voting through Oct 31, newsletter PDF).
- AR: Washington County (0; Madi and Russell, Election Commission page). Arkansas had no sticker story before this.
Every entry carries `until` 2026-11-03 (LA Oct 31, Charleston Oct 31) with past-tense `en_after`/`es_after`; checked in a
`VOTE_TODAY=2026-11-04` build (SC, CA, NY, NC read in past tense).

**Rejected or not shipped, and why.** NYC Halloween, Marathon and Future Voter stickers for 2026 (the highest-demand item):
news only (PIX11 Oct 23, Time Out Oct 26); the BOE's X search needs a login, vote.nyc has nothing, and its 2025 annual report
covers 2025 only. San Francisco's 2023 contest art (Hollis C.): 2026 use not stated, so only the mail-packet sticker shipped.
Chicago and Cook County: no official 2026 design. Wake: 2023 early-voting sticker only. Warren County NY: winner (Charlotte)
only in a Facebook post we could not read; dropped. Dutchess winner (Camille), Collier oranges detail, Washington County AR
schools, mockingbird: news only, left out of the entries. Hillsborough FL (Angelica, Durant High): newsflash 41 now 404 and the
VBM page has no sticker line. Pinellas "I Voted...By Mail" digital sticker: votepinellas.gov blocks curl, WebFetch and the
Chrome extension (permission denied). Harris astronaut, Dallas 2024 reuse, King County mail-packet cut, Travis bluebonnet,
Chatham NC, Manatee (Boys & Girls Clubs), St. Louis County (school district source, not the election office), Washington
County AR fifth grader story: news only. Maricopa (2024), Philadelphia (2020): no 2026 statement. Allegheny: already the PA
story. Tarrant, Miami-Dade, San Diego handbooks: a plain sticker, nothing to tell. Gaines winner named only in the image (read).

**Build.** `facts_text.py`: `sticker.more` kind, each entry needs place (en, es), kind (county, city, parish), en, es, until,
en_after, es_after, an official src, a quote and a checked date within 21 days; banned words and em dashes checked; source
names required. `ALLOW_HOSTS` adds jcncelections.org and www.fostercity.org; new `ALLOW_ACCOUNTS` allows one office account
(x.com/LACountyRRCC). `index.php`: `vote_more_stickers()` renders the H3 list inside #gallery after the two-path panel (or in
the sticker story section on a page with no gallery); the page's check date counts the entries. `qa_facts.py`: one block per
state with entries, links equal the entries' sources in order, the H3 follows an H2, no em dash, no block without entries.
`qa_es.py` allows "The Arts Based School" and "Art for Democracy". CSS: 7 lines. Mutation test: a news src, missing quote, old
date and an em dash each fail `--check`.

**QA.** facts, Spanish, SEO (local), events, save, cards pass; stale 28 (unchanged). Playwright at 390 and 1280 px, CA, TX,
AR in EN and ES: no horizontal scroll, visible headings run 1, 2, 3, 2 (no skipped level), gallery height unchanged when the
list scrolls into view. Screenshots looked at (phone CA EN, TX ES; desktop CA): spacing matches the paths panel, links
underlined. Ohio (no entries) renders the same as live apart from whitespace.

**Push 2 of Oct 30 (11:18 a.m.).** County stickers pushed after full QA; run 41088701829 succeeded, IndexNow 200 (the run
submitted every page whose content hash changed, the 14 county-sticker pages EN and ES among them). Live: Texas, California,
Arkansas EN and ES, and Spanish New York show the new heading and list.

**Perplexity project housekeeping.** Moved into "VoteSticker.org Research" (move only, nothing renamed or deleted): the
turnout Deep Research and turnout Search threads, the post-election (Nov 4 to 30) thread, the earliest-sticker history thread,
the CA, FL, NY, NC and TX election-week Deep Research threads, the "i voted sticker / digital i voted sticker" thread, three "I
run VoteSticker.org ..." Deep Research briefs, and one "one page per state" Search thread (22h ago). Today's four threads were
created in the project. Not moved yet: about 15 more "I run VoteSticker.org ... one page per state" Search threads from Oct 28
and 29 (each move takes four clicks; the Library has no bulk move; finish next pass). Left out on purpose: GoDaddy staging,
catfish food, e-commerce outreach strategy, MVNO spam calls, piercing link building (not voting). The Chrome tab group dissolved
at the end; a Perplexity Library tab and a local preview tab may still be open outside it.

**Next.** NYC's 2026 specials if the Board posts them on vote.nyc (highest demand still unanswered); Pinellas digital sticker
needs a person to read votepinellas.gov/ivoted; Hillsborough (Angelica) if the release comes back; more states (IL, MI, PA, OH,
GA, VA by page impressions); item 2 mail answers.

### October 30: Pass 2, Last-Minute Mail Answers

**Order.** Search Console, Oct 23 to 29, page impressions with EN and ES summed: TX 823, CA 671, FL 606, NY 439, NC 425,
OR 402, MD 365, WA 335, LA 328, NJ 231, CO 211, UT 200, AK 196, OH 195, TN 179, SC 178, MI 173, MN 172, IL 156, AZ 149,
MA 142, GA 125, NM 122, PA 110, VA 108, WI 99, KS 94, CT 91, AL 81, HI 78, MO 75, AR 72, IN 68, OK 66, NV 65, ID 56, ME 55,
DC 54, NE 44, MS 38, MT 38, VT 33, RI 29, KY 24, IA 23, WV 22, DE 22, SD 19, WY 18, NH 12, ND 3. Before this pass, 27
states had all three answers from Oct 21 to 29 reads; 18 had no late-mail answer, 6 no tracker and 5 no provisional
answer. Answers that still read right were left alone; the pass filled gaps and fixed thin answers, working down the list. In the facts files the three questions are `faq.late_mail`, `faq.track` and `faq.provisional`
(the "name not on the list" route goes into the provisional answer, the way Texas does it).

**Research.** Perplexity project "VoteSticker.org Research". Deep Research is still locked: picking it opens "Upgrade for
advanced research capabilities. Upgrade to Max for enhanced access to Perplexity deep research." So five prompts ran in
standard Search inside the project, 22 s to 1 m 44 s each, 61 to 432 sources: p0 (changes Oct 23 to 30, all states), b1
(MD LA TN PA VA WI), b2 (OR WA CO UT NJ SC MN), b3 (KS CT AL AR IN OK NE), b4 (MS RI KY IA NH HI NV ME DE MO SD IL). The
project's mode button defaults to Computer; each prompt was switched to Search before sending. get_page_text cuts off long
answers; removing the earlier sections from the page and reading again gets the rest. Prompts and condensed reads in
`docs/research/mail-answers-20261030/` (p0, b1 to b4 answer files; edits-*.json hold every shipped answer with its quote).

**Past 7 days (p0, checked).** No deadline extension, emergency order or provisional rule change found. USPS began
"extraordinary measures" Oct 24 (extra collections, hand postmarks from Oct 26; no deadline change). Watson v. RNC
(24-1260): judgment reversed June 29, 2026, no new docket entry Oct 23 to 30, so postmark states stand. Boston closes all
city drop boxes Oct 31 8 a.m. to Nov 1 2 p.m. (city page; not added, local). Kansas: the SOS page says the Court of Appeals
denied the stay for the general, so postmarked-by-Election-Day ballots arriving by Friday Nov 6 count (undated; the Nov 2
dated check stays). NH: on Oct 22 the U.S. Supreme Court stayed a district court decision on student IDs; the SOS says
student IDs can't be used on Election Day (lead, not read; NH ID answer untouched).

**Shipped, push 1: 16 states** (each answer read on the official page today, by curl or in Chrome, quote in the facts file):
- Too late to mail (new): MD (postmark Nov 3, arrive Nov 13; drop box, local board or polling place by 8 p.m.), LA
  (registrar must have it by 4:30 p.m. Monday Nov 2), TN (receipt by close of polls; mail or carrier only, no hand
  delivery), PA (receipt 8 p.m.; county office or drop box; or surrender ballot and envelope at the polls), VA (postmark
  Nov 3, noon Nov 6; drop-off at polling places on Election Day under Code 24.2-707.1), WI (8 p.m.; clerk, polling place or
  absentee counting location), KS (court order: postmark Nov 3, arrive Fri Nov 6; any polling place in the county), CT
  (Town Clerk 8 p.m. by mail or drop box; in person by close of business Nov 2), AL (noon Nov 3 by mail; in person by close
  of business Nov 2; mail, carrier or own hand delivery only), AR (7:30 p.m. by mail; in person or bearer closed Fri Oct 30,
  with past-tense after-text), OK (7 p.m. by mail; hand delivery by end of business Nov 2 with ID), NE (8 p.m. CT / 7 p.m. MT;
  county office or drop box; agent allowed).
- Too late to mail (fixed for all-mail states): WA (drop box 8 p.m., county office or voting center, VoteWA.gov), OR (drop
  boxes until 8 p.m. Nov 3), UT (source moved to vote.utah.gov: received by 8 p.m., later ballots don't count).
- Name not on the list (new or rewritten provisional answers): WI (no provisional for that; register at the polls; ID
  provisional to Fri Nov 6 4 p.m.), WA (register in person until 8 p.m. and get a regular ballot), CO (register and vote at a
  VSPC through Election Day; wrong-county provisional doesn't count; receipt explains status), OR (Provisional Ballot Request
  Form at the county elections office, 2026 Voters' Pamphlet).
- Track (new): AR VoterView ("ballot statuses").

**Push 1 (12:03 p.m.).** Full QA passed (facts, Spanish, SEO local --no-legacy, events, save, cards; stale 28, unchanged).
Run 41095490508 succeeded; IndexNow HTTP 200, 32 URLs (the 16 states EN and ES). A scheduled run (41094732990) that
started on the old commit deployed first and failed only at its commit step (my push had landed); no harm. Live: Maryland
and Kansas EN and ES show the new answers.

**Shipped, push 2: 7 states.** Too late to mail: KY (6 p.m. local; county drop box), IA (auditor by 8 p.m.; not the polling
place, but you can surrender it there), RI (8 p.m.; any drop box, the polling-place receptacle on Election Day, or the Board in
Cranston), NH (5 p.m. Nov 3 by mail or agent; in person by 5 p.m. Nov 2), MS (postmark Nov 3, Circuit Clerk by 5 p.m. Nov
10). Name not on the list: KY (provisional; Provisional Voter Information link), HI (provisional; county or Office of
Elections), DE (federal offices only; status by number). Track: MS My Election Day (absentee and affidavit ballots).
`qa_es.py` allows "My Election Day" (tool name). Push 2 (about 1:10 p.m.): full QA passed (cards 79,920 WebKit cards in
the circle); run 41098468157 succeeded; IndexNow HTTP 200, 14 URLs (7 states EN and ES). Live: Iowa and Kentucky EN and ES
show the new answers.

**Post-election and Nov 2 checks.** `VOTE_TODAY=2026-11-04` build: every "Is it too late to mail" answer drops (page and
FAQPage markup) in MD, LA, KS, WA, WI, AR, ES Oklahoma; tracking and provisional answers stay, as set Oct 25 (they still
apply to counting). `VOTE_TODAY=2026-11-02`: all present; Arkansas shows the after-text ("closed at close of business on
Friday, October 30"). Note: the WA provisional answer says you can register "until 8 p.m. on Election Day"; after Nov 3 it
reads as general information, not an instruction for this election.

**Open Fact Calls.**
- Arkansas hand delivery: the SOS absentee page and 2026 calendar say in person or by bearer by close of business Friday
  before the election; the SOS general FAQ still says the day before (in person) and 7:30 p.m. Election Day (bearer). We
  follow the absentee page.
- Tennessee and South Carolina: no state page found saying what happens when your name isn't on the list (Shelby County
  only for TN; SC FAQ covers moves and ID). Provisional answers left as they were.
- Minnesota: no state page read that answers "What is a provisional ballot" (MN uses Election Day registration; the
  Register on Election Day page is read and verified). Provisional answer still missing; the reg FAQ covers it.
- New Hampshire provisional: the 2024 implementation memo ended affidavit ballots; current challenged-ballot route not
  verified. Mississippi provisional (affidavit ballot): only a handbook URL from Perplexity; not read.
- Iowa provisional and Maine, Nevada, Indiana and Nebraska "not on the list" lines: leads in b3 and b4, not shipped.
- Track: no statewide tool for IL, IN, MO, SD. IL leads: Chicago, suburban Cook ("What Is My Mail Ballot Status", read),
  DuPage BallotTrax, Lake Voter Power, Will County. Could ship as a county list after Garrett's OK on the format.
- Mississippi hand delivery: an older county handbook says it is illegal to hand deliver absentee ballots (not read).
- NJ cure deadline: still unresolved (timeline Nov 14 vs. poster "48 hours prior to certification"); the page keeps
  "Return it as soon as you can." N.J.S.A. 19:63-17 could not be read on njleg (no plain statute URL found).
- ID affidavit routes (AK AZ IA ND OK WY) and ALLOW_HOSTS (Camden NJ, Washington County MD, Johnson and Montgomery County
  KS): not reached this pass.
- Sticker lead: Rhode Island's mail packet includes an "I Voted by Mail sticker" (vote.sos.ri.gov/Voter/VoteByMail, read
  in Chrome today).

**Perplexity Library.** Moved 13 "I run VoteSticker.org ..." threads (Oct 29, one page per state and home page) into the
project with each row's "..." menu, Add to project (move only). The bulk select bar offers only Mark as unread and Delete,
so it wasn't used. More of the same Oct 28 to 29 threads remain below them (about 20 or more).

**States left.** All 51 have a late-mail answer except Indiana (its mail-deadline answer already says the 6 p.m.
receipt rule). Missing tracker: IL, IN, MO, SD. Missing provisional: MN, MS, NH. Thin "name not on the list" lines:
TN, SC, NJ, NE, NV, ME, IA, IN.

### October 30: Garrett's Calls After Pass 2
Garrett accepted Claude's recommendations ("I trust your recs"):
- Illinois: list county ballot trackers for the biggest election authorities (Chicago, suburban Cook, DuPage, Lake, Will, Kane), each read on its own site.
- Arkansas: where the state's own pages give different hand-delivery deadlines, show the earlier one and tell voters to check with the county clerk.
- New Jersey cure deadline: leave "Return it as soon as you can"; no phone call.
- Social posts as sources: allowed when the post is on the election office's own official account and the office has no web page saying the same. Record the post URL, date and exact quote. Prefer the web page when one exists.

### October 30: Pass 3, ID Routes, Provisional Answers, Fact Calls

**Research.** Perplexity project "VoteSticker.org Research", four prompts, all in standard Search (28 s to 2 m 22 s,
85 to 211 sources): A (ID routes), B (name not on the list), C (IL trackers, IN MO SD trackers, AR deadline, county
sites, RI sticker), D (county sticker stories for the four ALLOW_HOSTS counties). Deep Research tried once first: the "+"
menu now lists "Deep Research, Included in Pro", but picking it opened "Upgrade for advanced research capabilities.
Upgrade to Max for enhanced access to Perplexity deep research." Not retried. One slip: prompt C's first send went out in
Computer mode (the mode pill resets to Computer when the project page reloads, and a click before it finishes loading
flips it back). Stopped after 7 s; the task's Usage panel read "Session usage: Included", so no purchased credits. Every
later send was checked on a zoomed screenshot of the pill first. Answers and prompts in `docs/research/pass3-20261030/`.
Past 7 days: no ID, provisional or deadline change found in any of the states asked about. Every fact below was read on the
official page (curl, or Chrome where curl is blocked), quote in the facts file.

**Push 1 (item 1, ID routes).** Delaware's answer is the model: what to do with no ID, in the ID answer itself.
- Wyoming was wrong. The FAQ ID answer listed IDs only, and the "without ID" line said only "get a free Wyoming ID card",
  which reads as no way to vote. W.S. 22-2-119: a voter who can't show ID "may vote by provisional ballot"; 22-15-105:
  oath, and proof to the county clerk by close of business the day after. Both now say so (Nov 4 deadline). Statute read
  in the Title 22 PDF on wyoleg.gov.
- North Dakota was incomplete: the FAQ ID answer left out the set-aside ballot (the facts box had it). Added from the SOS
  Forms of Voter ID page (13 days, county auditor, before the canvassing board).
- Alaska (poll worker who knows you can waive ID; questioned ballot), Arizona (conditional provisional), Iowa (a registered
  voter from the precinct vouches by oath; regular ballot), Oklahoma (sworn affidavit, provisional): already right.
- The Election Day banner carries hours and lookups only, no ID text; checked.
- Commit b64d718. Full QA passed (facts, Spanish, SEO local --no-legacy, events, save, cards; stale 28, unchanged). Run
  41116072622 succeeded; IndexNow HTTP 200 in the run. Live: Wyoming and North Dakota, EN and ES, show the new lines.

**Push 2 (items 2 to 6).**
- Name not on the list (`faq.provisional`), new: MN (Election Day registration with one proof of residence, or a registered
  voter from the precinct vouches; Chrome), MS (affidavit ballot when your name isn't in the poll book; written notice of how
  to check; 2026 Voter Information Guide PDF), NH (register at your polling place with proof of identity, age, citizenship
  and domicile; sos.nh.gov register page, in Chrome; it carries an Oct 22 notice on the Supreme Court's student-ID stay).
- Strengthened: TN (claim you're registered in the county, registration form, proof of address, affidavit, provisional;
  no Election Day registration; Tenn. Code 2-7-112(a)(3) in the General Assembly's code bill PDF; photo ID line kept),
  SC (name not on the poll list leads; SEC voter information poster), NJ (1-877-NJVOTER after the election, from
  vote-provisional-ballot.shtml; cure answer untouched per Garrett), NE (first line: the right to a provisional ballot if
  your name isn't on the list), NV (register at any polling place in the county until close, or provisional; NRS
  293.5847 and 293.3081), IA (name not on the list, notice, proof by noon Monday Nov 9, letter if not counted; Chrome).
- Trackers (`faq.track`): IL county list, each read on its own site: Chicago Voter Information Lookup (Chrome),
  suburban Cook "What Is My Mail Ballot Status" (curl), DuPage BallotTrax linked from the Clerk's Vote by Mail page (curl),
  Will and Kane BallotTrax pages (Chrome; county clerk branding). Lake left out: its Voter Power page shows a lookup and VBM
  request, no status wording we could read. IN: statewide after all, "Get Absentee Ballot Information" on
  indianavoters.in.gov (2026 Election Administrators Manual); `mail.track_url` added, so the paths panel and banner link it.
  SD: statewide Voter Information Portal ("ARE YOU ABLE TO TRACK YOUR ABSENTEE BALLOT USING THIS PORTAL: Yes"); the track
  URL was already set, the answer was missing. Pass 2 logged both as having no tracker; that was wrong.
- Arkansas (Garrett's call): the SOS FAQ now matches the absentee page and calendar (in person and bearer by close of
  business Friday Oct 30), so the conflict is gone. Added the authorized-agent route (hospital or nursing home patients,
  7:30 p.m. Election Day) and "Not sure? Check with your county clerk." to the answer and its after-text.
- ALLOW_HOSTS: camdencounty.com, washco-md.net, jocoelection.org, jocogov.org, mgcountyks.org (with and without www), each
  confirmed by its home page title. County links they had blocked: only Johnson County ships. Its contest packet says the
  four winning designs go out "at Advance Voting locations, in Advance Mail Ballot envelopes and on Election Day at all
  polling sites"; no winners are named, so none are named here (Kansas `sticker.more`, demand 0). Camden (March contest
  announcement), Washington County MD (January public vote) and Montgomery KS (rules dated for a Dec 2025 judging): no 2026
  winner or Nov 3 use announced; not shipped.
- Rhode Island sticker: it was already in `sticker.mail` since Oct 24; reread in Chrome today ("Your mail ballot packet
  includes: ... I Voted by Mail sticker") and rewritten to list what's in the packet.
- Missouri tracker: no statewide tool for domestic voters (local election authority; the MOVE portal is military and
  overseas only). Logged, not shipped.
- Commit 5093238. Full QA passed (as above; stale 28). Nov 2 and Nov 4 builds checked for AR IL IN SD MN TN KS RI WY ND:
  answers show on Nov 2; late-mail drops on Nov 4, tracking and provisional stay; Johnson County and Arkansas switch to
  past tense. Run 41118194726 failed at its own QA step, before deploy: `qa_es.py --base` flagged "by Mail" on Spanish
  Rhode Island (straight quotes around "I Voted by Mail"; qa_es only skips English inside curly quotes). My local qa_es ran
  without `--base` and missed it, so local QA now runs it with `--base` like CI. Fixed with curly quotes (also in the IL
  and IN Spanish tracker answers) in commit 88cd058, full QA again (all pass, stale 28). Runs 41119679191 and 41120367499 then built, passed local QA
  and deployed, but failed at "QA live": the runner's requests to votesticker.org dropped (IncompleteRead, then
  RemoteDisconnected), so their IndexNow and records steps were skipped. Live QA from here passed (qa_seo --no-legacy,
  qa_es, qa_facts), and `scripts/indexnow.py` sent the 30 changed URLs (15 states, EN and ES): HTTP 200;
  build/indexnow-sent.json committed. Live: Illinois and Minnesota EN and ES, Spanish Rhode Island and Kansas
  (Johnson County) show the new text. Watch the Oct 31 6:05 a.m. run: if "QA live" fails again, it is the runner's
  connection, not the pages.

**Open Fact Calls.**
- Arizona ID deadline: the SOS page says "the 5th business day" (Perplexity counts Nov 10); our page says Sunday Nov 8 from
  the session law and the SOS Master Elections Calendar (read Oct 28). Kept Nov 8.
- Lake County IL tracker: not on the page until a Lake County page says the lookup shows mail ballot status.
- Missouri: no statewide tracker; county route only, not named.
- Maine and Indiana "not on the list" lines: not reached (Maine needs a page that says what happens when your name is
  missing; 21-A §122(4) Election Day registration is read and could go in).
- NH: our ID answer already says student IDs aren't accepted; the Oct 22 Supreme Court stay notice on sos.nh.gov agrees.
- Nevada's provisional status page (nvsos.gov) shows a CAPTCHA in Chrome; skipped, statute used.

**Perplexity Library.** Moved 40 more "I run VoteSticker.org ..." Search threads (Oct 28 and 29, one page per state and
home page) into the project, one at a time with each row's "..." menu, Add to project. Nothing renamed or deleted. Every
voting thread in the Library now sits in the project; the non-voting ones (MVNO spam calls, piercing link building,
GoDaddy staging, catfish food, outreach strategy, Freelance Finder work) stay where they are.

**Next (after the Sunday Nov 1, 7 p.m. freeze: fact fixes and content inside existing slots only).**
- Nov 1: index snapshot (`archive_search.py --index`); daily ended-early-voting check.
- Nov 2: reread the Kansas advance voting page (court order); dispatch the build by hand if none by 7 a.m.; banner check.
- Nov 4: post-election build deployed by 7 a.m. (dispatch with `2026-11-04` if not); spot-check EN and ES.
- Nov 5: remove the `30 6 4 11 *` cron; log the release in `docs/change-log.csv`.
- Item 4: Spanish wording ("lugar de votación" vs "casilla", "Ya voté" vs "Yo voté"), decided by evidence.
- Item 5: Michigan page order (official 2026 designs after our gallery; trim repeated facts).
- Item 6: results certification per state after Nov 3 (`dates.certify` proposal; each date read on the state's own page).
- Item 7: history, notable designs prompt.
- NYC's 2026 Halloween, Marathon and Future Voter stickers: recheck vote.nyc and the BOE for an official post.
