# Page Improvement Plan, October 23, 2026

What to change on the VoteSticker pages between now and Election Day, in order. It draws on Search
Console (October 19 to 23), the Perplexity digest (`docs/research/perplexity-20261023.md`), the 50-state
early voting report (`docs/early-voting-research-2026-10-23.md`) and the roundup comparison
(`docs/sticker-roundup-competitive-research-2026-10-23.md`). Two more research runs are coming; they
slot into Phases 2 and 3.

## What Success Looks Like

- Every date, hour and sticker claim on a page traces to an official page we read, with the date we read
  it. Nothing about a state rests on one county's practice.
- The pages answer what people searched for, in the first screen: "[state] i voted sticker", "i voted
  early sticker", "how to get i voted sticker".
- The pages already on page one get clicked more. Most sit at positions 5 to 8 with a 5 to 10% click rate.

Measure each change against `docs/change-log.csv` and the BigQuery archive: impressions, clicks and
position for the changed pages, two weeks before and after.

## Writing Standard for Every Page

These rules apply to new copy and to any existing line we touch. The current pages mostly pass. A scan
of the built English pages on October 23 found no em dashes and no staged contrasts. Its "not ... but"
hits are real conditions ("You don't need photo ID to register, but you'll show it before you get a
ballot") and stay.

| Don't | Instead | Example |
|---|---|---|
| Open with a run-up: "Here's what you need to know," "Good news," "The short answer" | Start with the answer | "Early voting in Texas runs October 19 to 30." |
| Contrast with something nobody claimed: "It's not just a sticker, it's a statement" | State the fact | "Wake County's 2024 early-voting sticker was a sixth grader's unicorn." |
| Borrowed verbs: a page *carries* a date, a box *holds* the rules, a link *lands* you, a fact *sits* | Plain verbs: has, lists, shows, goes to, is | "The county page lists each site's hours." |
| Clipped tails: ", no guessing", ", no account" | A full clause, or cut it | "You don't need an account." |
| Hedges with no source: typically, generally, likely | Say who it applies to, or drop the claim | Keep "Usually not" for ID only where the state's rule makes it true for most voters, then name the exception |
| A state claim built on one county | Name the county and the year | "King County mailed a sticker with every ballot in 2024 and 2025." |
| A closing line that repeats the section | End on the last fact | |

Voice: second person, short sentences, the voter's next step. Spanish follows `docs/spanish-style.md`
(tú). The humanizer pass runs on every draft before review.

## Phase 1: Accuracy Before Texas Early Voting Ends (October 24 to 29)

Each fix goes through the facts files and `qa_facts.py`, from the official page, never from the
research text alone.

| # | Change | Pages | Source status |
|---|---|---|---|
| 1.1 | **"Early voting has ended" copy** (Plan Section 5.1). Titles, cards and summaries switch the day after each state's window closes. Louisiana closes October 27, Texas October 30. | All in-person states | **Built October 23**: `vote_close_early()` in `site/index.php`; test any date with `VOTE_TODAY=2026-10-31 python3 build/build_static.py` |
| 1.2 | Maine Oct 5 to 29 and Nebraska Oct 5 start | ME, NE | **Done October 23** (`0e1c9fc`) |
| 1.3 | South Carolina hours, 8:30 a.m. to 6 p.m. | SC | **Done October 23** |
| 1.4 | South Dakota: in-person absentee from September 18 | SD | **Done October 23** (Sep 18 to Nov 2; SDCL 12-19-1 makes it open to any registered voter) |
| 1.5 | Wisconsin: October 20 to November 1, each town sets its days | WI | **Done October 23** from Wis. Stat. 6.86(1)(b) |
| 1.6 | Utah and Michigan: say counties or communities can add days | UT, MI | **Done October 23** |
| 1.7 | California, Colorado and Hawaii: add in-person dates beside the mail dates (CA Oct 5 and vote centers Oct 24; CO centers Oct 19; HI service centers Oct 20) | CA, CO, HI | From the 50-state report; confirm each |
| 1.8 | Early voting links that go to Election Day lookups: point them at the early voting directory | AK, PA and others flagged in the report | Report lists them |

## Phase 2: Answer the Sticker Questions

**Progress, October 24:** answers live for TX, NC, SC, MO, FL, NY, VA, GA, IL, NV, IA, NM, OH, RI (early) and WA, OR, MI, CO, CA, KS, DC, MA, RI (mail), plus Vermont's contest story (batch 2, October 25). No official 2026 source yet for AK, AL, AR, CT, DE, HI, ID, IN, KY, LA, MD, ME, MS, ND, NH, PA, SD, TN, WV, WY; held partials are in `docs/research/loop-batch2-*.json`. on Every State Page (October 26 to 30)

Search Console shows these queries on page one with few clicks: "i voted early sticker" (about 110
impressions across 15 states), "[state] i voted sticker" (every state) and "how to get i voted sticker"
(Texas, Oregon, Washington). The pages have the maker and the voting guide. What they don't have is the
answer to the sticker question itself.

**2.1 Two new FAQ answers on every state page**, national answer first, then the state's named
examples where we have an official source:

- "Do I get a sticker if I vote early in [state]?" Many offices hand out stickers at early voting; the
  wording and design vary by county. Then the state's documented examples (Harris County's astronaut,
  Wake County's unicorn, Hamilton County's "I Voted Early!"), each with its year.
- "I voted by mail. How do I get a sticker?" Check the instruction sheet in your packet; some counties
  put it there. Then the named counties: Thurston, King and Pierce (WA), Salt Lake and Grand (UT),
  Douglas, Larimer and Gilpin (CO), Santa Clara (CA), DC, Snohomish pickup sites. Finish with "make a
  digital one here," which is true everywhere.

Every named example needs an official county or state page. Examples sourced only to news or photo
captions (Hamilton County, Harris County, Pierce County so far) stay off until we find one.

**2.2 A "Where to get a physical sticker" block** on the mail-heavy pages (WA, OR, UT, CO, CA, NV, HI,
DC), named counties only. For Oregon: the state and Multnomah offer virtual stickers, and Deschutes
makes physical ones. The Portland volunteer who mails stickers is a private person; linking her
needs your OK.

**2.3 The state's own 2026 sticker story** in the existing tradition section. 29 states already have
one. The research adds sourced 2026 designs for Michigan (nine winners), North Dakota ("You Betcha, I
Voted"), Kentucky, New Hampshire, Alaska, Alabama and Ohio (America 250), Montana, West Virginia,
Louisiana (Terrance Osborne) and Georgia (Steve Penley). Name designs and adult artists; leave out
minors' names unless an official release prints them.

**2.4 Titles and descriptions for the page-one pages.** Today: "Texas I Voted and Vote Stickers: Early
Voting Oct 19–30." Test the searched phrase on five pages first (TX, NC, CA, WA, OR): for example
"Texas I Voted Early Sticker, Free: Early Voting Oct 19–30" during early voting, then "Texas I Voted
Sticker" after it closes. Compare click rate after 7 days with the other states as the control. The
65-character limit and `qa_seo.py` still apply.

Writing order for 2.1 to 2.3: Texas, North Carolina, Washington, Oregon, California, Maryland, New York,
Florida, then the rest by impressions (`docs/search-console-priorities-20261023.html`).

## Phase 3: Win the Home Page Query (October 28 to November 3)

The home page is at position 7 to 8 for "i voted stickers by state" (65 impressions, 2 clicks). The
pages above it (Ballotpedia, CNN, BuzzFeed, AJC) are galleries of official designs with captions. Ours
is a maker. The roundup research recommends a gallery of official, election-issued designs.

**3.1 "I Voted Stickers by State: Official Designs and Their Stories"**, either a section of the history
page or a new page (your call on the URL). One card per design: state, issuing office, year, designer if
documented, how it was chosen, one sourced fact, link to the state page. Start with the states we have
sourced; say how many are covered. Never fill a state with an invented or generic design.

**3.2 Images need a recorded basis.** An official page proves a design was issued, not that we can
show the picture. For each image: the office's permission, a license (Wikimedia Commons file by file),
or no image (describe the design and link to the office's page). Our own maker stickers get a clear
"made by VoteSticker" label, so nobody reads them as official.

**3.3 Home page:** a short lead into the gallery above the FAQ, and an FAQ answer to "Is there one
official I voted sticker per state?" (No. Many are chosen by counties, and some states issue a statewide design; name both kinds.)

## Phase 4: Pages With No Impressions

- Colorado, North Dakota and 9 Spanish pages had Request Indexing on October 23; `/es/wyoming/` is
  still to submit. Re-check all 12 with `scripts/archive_search.py --index` around October 27.
- Tennessee, Idaho, New Hampshire and Wyoming are indexed with zero impressions. Phase 2 content goes to
  them in impression order like every other page. Don't write extra for them before we know whether it's
  indexing lag or low demand.
- Spanish pages: same Phase 2 answers once the English is reviewed, through the es-mx skill and a
  native-speaker check.

## After November 3

- Post-election copy (resume doc item 5) and a "results and recap" page are out of scope here.
- Keep the gallery and the state sticker stories; they stay useful between elections and become the
  2028 starting point. Re-read every fact against the next season's sources (the facts files fail on
  stale check dates by design).

## Decisions (October 23)

Garrett asked for recommendations; these are the calls, with the reason.

1. **South Carolina closes at 6 p.m.** S.C. Code 7-13-25(F)(1), the 2026 early voting block on
   scvotes.gov and the Voter Go-To Guide all say 6; only an older general block on the same page says 5.
   Shipped October 23 (facts file and card note).
2. **No link to the Portland volunteer.** The site links official sources only, and a private person's
   inbox can't be checked or kept current. The Oregon answer names the state's virtual sticker and
   Deschutes County's physical one, then the maker.
3. **The official-design gallery gets its own page** (working URL `/official-stickers/`, Spanish
   `/es/official-stickers/` once reviewed). One page per search intent: the home page is the maker,
   `/history/` answers where the sticker came from, and the gallery answers "what does each state's
   sticker look like." The home page and every state page link to it; it links back to both.
4. **Change titles on every state page at once, inside the early-voting-ended work (1.1).** Each title
   has to change when its window closes anyway, so one change per page beats two, and a five-page test
   can't produce a readable result before November 3. Log each state's change date in
   `docs/change-log.csv` and compare by state in BigQuery afterward.

Later research to fold in: `docs/early-sticker-research-2026-10-23.md` (exact "I Voted Early" wording
in Guilford County NC, Minneapolis and Milwaukee from official pages; 19 states unresolved). It feeds
Phase 2.1, starting with Texas and North Carolina.
