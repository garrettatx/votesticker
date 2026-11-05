# Loop: Results Links and Legal Deadlines

Started November 5, 2026, at Garrett's request, to follow the results pass in `LOOP-county-and-mail.md`. One agent at a
time; log each pass at the bottom.

**Goal of every item:** a voter who lands on a state page between now and December 1 can reach the state's results and can
tell what the state has actually said about timing.

## Rules
- The rules of `LOOP-county-and-mail.md` hold: official sources with the wording read on the page, Perplexity as a lead,
  EN and ES (tú), no em dashes, Title Case headings, one agent at a time, no Firecrawl.
- In Perplexity, switch the composer from Computer to Search before sending (Garrett, Nov 5). Computer is preselected.
- Bot check or 403 in Chrome: stop on that URL and log it. No CAPTCHAs, no spoofed headers.

## Sourcing Rule for Dates (Nov 5)

Garrett passed this question to Perplexity, and this is its recommendation, with the New York text read on the page.

| What the page says | Source it needs | Wording |
|---|---|---|
| Legal deadline | A current statute published by the state | Name the exact action the law requires, and say it's a deadline |
| Scheduled certification | An election office page or notice printed for 2026 | "is set for", keeping the source's hedges |
| Certified | An official record that it happened | Past tense, with that record as the source |

A statute never becomes "results are official on" or "results become final". Recounts and court orders can change results
before or after certification. A day-count rule also needs care: NCSL's New York row says "the 15th day after each general
election", while the statute says the fifteenth of December. Read the statute, not a summary. An election office date
replaces a statute deadline as soon as one can be read.

Thread: https://www.perplexity.ai/search/ac2e1c47-925f-4040-bc59-d3f57a2cd3fc

## Items

| # | Item | Evidence | Status |
|---|---|---|---|
| 1 | New York: statute deadline under the new rule | Election Law 9-216(2), read in Chrome | Built Nov 5, not pushed |
| 2 | Results links for AZ, NH, NV, NY, TN, WI | The six held back Nov 4 | Built Nov 5, not pushed |
| 3 | Link label: "Official [state] election results" points at pages headed "Unofficial" on election-night sites (NY, AZ seen) | The source is official, the totals aren't yet | Done Nov 5: "[State] election results" / "Resultados electorales de [Estado]" (Perplexity, thread below) |
| 3b | Availability note where the link opens a page with no November totals yet (TN, WI read Nov 5; IL, KS, KY, MA, ME, MI, MS, NJ from Nov 4). Per-state fact with a recheck date, so it can't go stale | NN/g: a link should set the right expectation of the page | Open |
| 4 | Statute deadlines for the other states with no date: AR, AZ, CO, CT, GA, HI, IA, LA, MA, ME, MO, NH, NV, OH, PA, RI, TN, WV. One Perplexity prompt for leads, then read each statute. Skip any state whose office prints a date (item 5) | No demand number yet: pull Search Console for results and certification queries first | Open |
| 5 | Chrome reads of the election office date pages blocked Nov 4. GA and IA have snippet leads (Nov 20, Nov 30), not reads | Nov 4 log | Open |
| 6 | Recheck the dates left off on purpose: DE, IN, SC, SD, VT. Under the new rule, IN, SD and VT may have a statute deadline that fits; SC's sources still conflict | Nov 4 log | Open |
| 7 | Run the history designs prompt, still unrun: `docs/research/history-postelection-20261029/designs-prompt.txt` | "i voted sticker history" 133 impressions at 8.7 | Open |
| 8 | Dated checks due Nov 5: Wyoming's provisional answer, the Search Console read-back, the digital sticker citation check in ChatGPT and Perplexity | Resume block, `LOOP-county-and-mail.md` | Open |
| 9 | Rewrite `qa_events.mjs` post-phase flows; it stops at `#copy` | Nov 4 QA | Open |

Each new date ends its `until` no later than the date itself, with past-tense text, like the Nov 4 entries.

## Log

### November 5: Pass 1, the Statute Question

**Perplexity.** Project "VoteSticker.org Research", Search mode, one prompt, finished in 24 seconds. Its answer: a current
statute is enough for a labeled legal deadline, but not for a scheduled or actual certification date. It also corrected
our framing. New York's law doesn't fix a certification date; it says the board meets by December 15.

**Read on the page (Chrome).** N.Y. Election Law 9-216(2), nysenate.gov, most recent revision 2014-09-22: "The state board of
canvassers shall meet on or before the fifteenth day, or, in a year when electors of president and vice-president are chosen
on or before the first Monday after the first Wednesday, of December next after each general election" and "The board may
adjourn from day to day, not exceeding a term of five days." 2026 has no presidential electors, so the fifteenth applies.

**Not confirmed, taken from Perplexity only.** Ballotpedia's New York row says December 9, 2026 (not official; Perplexity
could not open the page either). The Board's December 9, 2025 meeting transcript. The EAC, NCSL and Washington descriptions
behind the rule. Two guessed URLs for the Board's calendar returned its 404 page, so no Board date was found.

**Results links, read in Chrome.**
- NY `nyenr.elections.ny.gov`: live 2026 totals, headed unofficial; linked from the Board's site banner.
- AZ `results.arizona.vote`: lists the 2026 general, unofficial; the Secretary of State links this host. Added to `ALLOW_HOSTS`.
- NV `silverstateelection.nv.gov`: live results.
- NH `sos.nh.gov/2026-election-results`: lists the 2026 general. The URL tried first returned Forbidden; this one came from
  the links on the office's elections page.
- TN `sos.tn.gov/elections/results`: landing page; the newest entry is August 6, 2026, no November results yet.
- WI `elections.wi.gov/elections/election-results`: landing page; the newest entry is the 2026 partisan primary. The page says
  Wisconsin has no statewide election night reporting.

**QA (local, build dated Nov 5).** Pass: `facts_text --check`, `qa_facts`, `qa_es`, `qa_seo --no-legacy`, `qa_stale`. All six
sections read in the built EN and ES pages. A Dec 16 build caught a problem: `qa_stale` flagged New York's past-tense "to meet
by December 15", and CI runs that check, so the deploy would have stopped if the section were still up then. Reworded to "set
December 15, 2026 as the latest date". Not run: `qa_events` (item 9), browser suites. Not committed or pushed.

### November 5: Pass 2, the Link Label

Garrett asked for this to go to Perplexity. Search mode, 22 seconds, thread
https://www.perplexity.ai/search/801fce6b-d314-42dc-afa2-7426eb125102. It recommends dropping "official": the label names the
topic, and the sentence above the link gives the status. Its reasons: the EAC separates unofficial results on state sites from
certified ones; NN/g and WCAG on descriptive link text. The EAC, NN/g and WCAG points are as Perplexity reports them, not read
here. The Spanish is its proposed wording, not an agency's. Applied in EN and ES.
