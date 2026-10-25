# Perplexity Prompts for State Early Voting Dates

Run each group in a new Perplexity thread. Paste the JSON back to Claude; every "computed" or
"low" answer gets checked in Chrome against the official page before it goes into `site/states.js`.

## Prompt Template

```
For the US general election on Tuesday, November 3, 2026, find the early voting rules for each of these: {STATES}.

Use only official sources: the state Secretary of State or state election board website, or the state statute. Use news or nonprofit sites (Vote411, Vote.org) only to find the official page, never as the source.

For each state, return one JSON object with these fields:
- "abbr": two-letter code
- "kind": "in-person" if the state has in-person early voting (including in-person absentee or "early voting by mail ballot in person"), "mail" if the state runs all-mail elections, "none" if neither
- "start": first day of in-person early voting for this election, YYYY-MM-DD (for "mail" states, the date ballots are mailed out)
- "end": last day of in-person early voting, YYYY-MM-DD (for "mail" states, the return deadline)
- "varies": true if counties set their own early voting dates or hours and there is no single statewide window, otherwise false
- "officialUrl": the official page where a voter finds their own early voting location or hours
- "officialLabel": short display name for that link, like "sos.ks.gov"
- "sourceUrl": the official page that states the dates
- "note": one plain sentence a voter needs, under 20 words (for example, "Vote at any early voting site in your county.")
- "confidence": "high" if an official page states the 2026 dates directly, "computed" if you calculated them from the statute's rule (say which statute in the note), "low" otherwise

If a state's dates come from a rule like "20 days before the election," give the computed dates and set confidence to "computed." Do not guess. If you cannot find something, use null.

Return a JSON array only, followed by a list of any states where sources disagreed.
```

## Groups

1. Kansas, Missouri, New York, Washington, California, Colorado, Virginia, District of Columbia, North Carolina, Pennsylvania (Garrett's friends; run first)
2. Alabama, Alaska, Arizona, Arkansas, Connecticut, Delaware, Florida, Georgia, Hawaii, Idaho
3. Illinois, Indiana, Iowa, Kentucky, Louisiana, Maine, Maryland, Massachusetts, Michigan, Minnesota
4. Mississippi, Montana, Nebraska, Nevada, New Hampshire, New Jersey, New Mexico, North Dakota, Ohio, Oklahoma
5. Oregon, Rhode Island, South Carolina, South Dakota, Tennessee, Utah, Vermont, West Virginia, Wisconsin, Wyoming

Texas is done (SOS Important Election Dates page, read October 19, 2026: October 19 to 30).

## Round 2 (October 19, 2026, afternoon)

Group 3 never came back, and 23 states came back "low" or without an official page Chrome could
confirm. This version asks for the exact sentence from the official page, so the Chrome check is
quick, and separates excuse-only absentee states.

```
For the US general election on Tuesday, November 3, 2026, find the early voting dates for: {STATES}.

Use only official sources: the state Secretary of State or election board website, or the state statute. Use other sites only to find the official page.

For each state, return one JSON object:
- "abbr": two-letter code
- "kind": "in-person" (any in-person voting before Election Day open to all voters, including no-excuse in-person absentee), "mail" (the state mails every voter a ballot), "excuse-only" (in-person absentee only with an excuse), or "none"
- "start", "end": YYYY-MM-DD for the statewide window, or null
- "varies": true if counties or towns set their own days
- "officialUrl": the official page where a voter finds their early voting location or hours
- "officialLabel": short display name, like "sos.ks.gov"
- "sourceUrl": the official page that states the dates
- "quote": the exact sentence from sourceUrl that states the dates or the rule, copied word for word
- "note": one plain sentence a voter needs, under 20 words
- "confidence": "high" if the quote states the 2026 dates, "computed" if you calculated them from a rule (name the statute), "low" otherwise

Do not guess. Use null for anything you cannot find. Return a JSON array only.
```

- A: Illinois, Indiana, Iowa, Kentucky, Louisiana, Maine, Maryland, Massachusetts, Michigan, Minnesota
- B: Arizona, Arkansas, Connecticut, Georgia, Hawaii, Idaho, Montana, Nebraska
- C: New Jersey, New Mexico, North Dakota, Ohio, Oklahoma, South Dakota, Tennessee, Utah
- D: Vermont, West Virginia, Wisconsin, Wyoming, Alabama, Mississippi, New Hampshire

Verified in Chrome after round 1 (October 19): TX, KS, MO, NY, WA, CA, CO, VA, DC, NC, PA, AK, DE,
FL, NV, OR, RI, SC. Record of what each official page said: `build/verified-20261019.json`.

## Round 3: Open Questions (October 23, 2026)

Questions the October 23 research left open, or whose only answer is a page we can't read
(automated reads blocked) or a news report. Run them over time; paste each answer back. A fact goes on a
page only once its official source is read. Every prompt below includes this line; keep it:

> Use only official sources (state or county election office, state statute) and quote the sentence
> that answers the question, with its URL and the date the page shows. Use news only to find the
> official page. No Firecrawl. If no official page answers it, say so.

**Dates and hours (Phase 1)**

1. South Dakota, November 3, 2026: last day and hours of in-person absentee voting at the county
   auditor's office. The SOS 2026 page gives only "Absentee Voting Begins: September 18, 2026."
2. Wisconsin, November 3, 2026: the official MyVote or Wisconsin Elections Commission wording for the
   in-person absentee window (October 20 to November 1?), and that each municipality sets its own
   days and hours.
3. Utah, November 3, 2026: may counties hold early voting past Friday, October 30, through Monday,
   November 2? Quote the Utah Code section or the Lieutenant Governor's page.
4. California, Colorado and Hawaii, November 3, 2026: first day of in-person voting at a county
   office or vote center (California October 5 and vote centers October 24; Colorado October 19;
   Hawaii voter service centers October 20). One quote per state.
5. Mississippi, November 3, 2026: did in-person absentee voting begin September 19 or September 21?
   Official sources disagree.
6. Montana, North Dakota and Vermont, November 3, 2026: the official sentence that tells a voter where
   and from when they can vote before Election Day (county or town clerk), even without one statewide
   first day.

**Stickers for early voters (Phase 2.1)**

7. For each of these states, a county or city election office page from 2026 that says early voters get
   an "I Voted" sticker, and what it says: Texas (Williamson County; the wilcotx.gov page blocks automated
   reads), Georgia (the Secretary of State's 2026 sticker announcement; sos.ga.gov blocks automated reads),
   Oklahoma (State Election Board), Michigan (any clerk), New York (any county board), Indiana, Florida,
   Virginia, Arizona, Nevada.
8. Minnesota: Minnesota Statutes 204B.49 says a sticker may contain the words "I VOTED," and nothing
   more. Minneapolis's early voting page promises an "I Voted Early" sticker. Is there an official
   explanation (statute amendment, SOS guidance, or a city page) that reconciles them?
9. Harris County, Texas: an official Harris County Clerk or harrisvotes.com page about the 2026 early
   voting astronaut "Mission accomplished!" sticker.

**Stickers for mail voters (Phase 2.1 and 2.2)**

10. Washington: official 2026 pages for King County (stickers mailed with ballots; the July 2025 release
    has it only in the headline), Pierce County (Mt. Rainier sticker with every ballot) and Snohomish
    County (pickup at the Auditor's Office, Sno-Isle Libraries and accessible voting sites).
11. Utah: Salt Lake County Clerk and Utah County Clerk pages saying the 2026 sticker comes in every mail
    ballot, and where to get a replacement.
12. Colorado: Douglas, Larimer and Gilpin County pages for the November 2026 general (sticker on the
    instruction sheet; Gilpin also at the polling center).
13. California: Santa Clara and Orange County Registrar pages for the November 2026 ballot packet sticker,
    and Merced County's "I Voted Early" mail sticker.
14. Nevada (Clark County), DC (Board of Elections) and Hawaii: the 2026 mail-ballot instruction text that
    mentions the "I Voted" sticker, or the official digital sticker.
15. Oregon: does any county besides Deschutes hand out a physical sticker for 2026, and where (counter,
    city hall)? Quote the county page.

**Official designs for the gallery (Phase 3)**

16. For each state that issues one statewide 2026 "I Voted" sticker: the state, the designer, how it was
    chosen, the official announcement URL and date, and whether the office allows the image to be
    reproduced (license or permission line). Start with Michigan, North Dakota, Kentucky, New Hampshire,
    Alaska, Alabama, Ohio, Montana, West Virginia, Louisiana, Georgia and Colorado.
17. Wikimedia Commons: which "I voted" sticker files from 2024 to 2026 have a free license, with the
    file's license line and the issuing office.

## Round 4: Sticker Answers Still Missing (October 25, 2026)

Loop batch 2 found no official 2026 source for these. Each answer needs an official state or county page
(URL, date, and the exact sentence), for the November 3, 2026 general election, not a primary.

18. Early voting stickers: for each of Alaska, Alabama, Arkansas, Connecticut, Delaware, Hawaii, Idaho,
    Indiana, Kentucky, Louisiana, Maryland, Maine, Mississippi, North Dakota, New Hampshire, Pennsylvania,
    South Dakota, Tennessee, West Virginia and Wyoming: does the state or its largest county say voters
    get an "I Voted" or "I Voted Early" sticker at early or in-person absentee voting in 2026? Quote it.
19. Mail ballot stickers: for Arizona (Maricopa, Pima), New Jersey (any county), North Carolina, Texas,
    Virginia, Utah (Salt Lake, Utah County), Wisconsin and Nevada (Clark): does the November 2026 mail
    ballot packet include a sticker? Quote the packet insert or the county page.
20. Oklahoma: has the State Election Board said whether the America 250 sticker is at November 2026 early
    voting (from October 28)? Its only release found so far is for the June primary.
21. Milwaukee (Wisconsin) and Lancaster County (Nebraska): their "I Voted Early" sticker documents return
    403. Is there another official page that says early voters get one in 2026?
22. California early voting: does San Luis Obispo County (or Los Angeles, Orange, Santa Clara) say its
    sticker is given at vote centers or the elections office during November 2026 early voting?
