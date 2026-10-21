# State Pages Content Strategy and Implementation Plan

Written October 21, 2026, 13 days before the November 3 election. Owner: Garrett Nafzinger. Implementer: a
smaller model working one state at a time from Section 8 (the per-state prompt) and Section 7 (the data
file). Nothing in this document touches the site; it describes what to build and in what order.

Sources for the numbers below: Ahrefs Keywords Explorer and SERP Overview (US, pulled October 21, 2026),
WebFetch reads of the ranking pages, and the repo itself. Where a number is missing, Ahrefs returned
none, and the plan says so.

## 1. Summary

**What a state page is today.** One PHP template (`site/index.php`) renders all 51 pages from one row in
`site/states.js` plus that state's block in `site/faq-states.php`. The live Texas page has about 765
visible words, nine H2s, one H1, a red ribbon with the key date, a gallery of ready-made stickers, the
maker, a 60 to 90 word "Before You Vote" panel, 3 to 5 state FAQs in `<details>` (polls, ID,
registration, ballot selfie, plus the mail deadline in all-mail states), 7 generic FAQs, related-state
links (same rule, neighbors, previous/next, all 51), a Spanish pair, and one JSON-LD block
(BreadcrumbList only, no FAQPage). Title: "Texas I Voted and Vote Stickers: Early Voting Oct 19–30".
Every fact on the page came from an official source read on October 19, with the URL shown.

**What it becomes.** The same page with six new server-rendered sections between the maker and the
related-state links: a dated deadlines table, how voting works in that state, where to vote, voter ID,
mail and absentee voting, and polling-place rules (ballot selfies, campaign gear, the state's I Voted
sticker tradition). The state FAQ grows from 3 to 5 questions to 8 to 10 and gets FAQPage JSON-LD. Target
1,500 to 2,200 words of body text per page, every fact with an official URL and a checked-on date in a
per-state JSON file that the template reads. Stickers and the maker stay above the fold.

**Expected effect, honestly.** The domain is 2 days old (launched October 19, 2026) with no measurable
Domain Rating. Official sites own the head terms: votetexas.gov holds 3 of the top 10 for "early voting
texas" and sos.wa.gov holds 9 of the top 10 for "washington ballot deadline", plus the featured snippet.
In 12 days the realistic wins are: (1) sticker queries, where the SERP is weak (KD 1 for "i voted
sticker", no profile-picture tool ranks, Google shows image thumbnails); (2) long-tail state questions
with KD 0 to 12 and thin SERPs ("texas voter id" KD 0, "can you take a picture of your ballot" KD 10,
"early voting michigan" KD 0, "early voting tennessee" KD 7); (3) being the page AI Overviews, ChatGPT and
Bing cite for state voting facts, because every answer is one sentence with a source. The head terms
("early voting texas" 6,400/mo, "early voting georgia" 2,600/mo) are a position 8 to 20 play after
Election Day indexing catches up, not a top-3 play before it. The content also keeps the pages useful
after November 3 when the sticker switches to "I voted".

**"10x the competitors."** The pages that rank are thin: votetexas.gov's early voting page is about 200
words with no calendar dates and no hours; Vote.org's Texas page is about 1,200 words of relative rules
("17 days before Election Day") with no dates; the CBS Texas article is 650 to 750 words and the only one
with dates, hours and ID in one place. A 1,500 to 2,200 word page with dated, sourced answers to every
question in Section 5 is 2 to 3 times the facts of the best competitor and 8 to 10 times the official
early voting page. It cannot beat the official site on authority, and should not try to replace the
official polling-place locator; it links to it.

## 2. Query Clusters Per Page

Volumes are US monthly averages from Ahrefs (October 21, 2026). Election-season volume is many times the
12-month average; Ahrefs' own "texas early voting 2026" at 4,400 shows the spike. "No data" means Ahrefs
has no row, which for these terms usually means under 10 searches a month averaged over the year, not
zero in October.

### 2a. Sticker Queries

| Query | Volume | KD | SERP notes | Page element that targets it |
|---|---|---|---|---|
| i voted sticker | 6,300 | 1 | Image thumbnails, news (contests), Amazon, Wikipedia, FVAP | Hub; on state pages the H1, gallery H2 and image alts |
| [state] i voted sticker / i voted sticker [state] | TX 20 to 30, CA 50, MI 450, OH 50, GA 40, MN 30, LA 30, NY 20, AL 10 | 0 where measured | Michigan: all 10 results are the SOS contest. Others: county contests, Etsy | H1, title, gallery H2, Section 5.10 (sticker tradition) |
| i voted early sticker | 60 | 0 | Image thumbnails, Etsy | Maker headline "I voted early"; FAQ "When should I switch to I voted?" |
| i voted by mail sticker | 30 | 0 | Etsy | Maker headline; all-mail state pages |
| i voted sticker png / transparent / clipart | 150 / 50 / 50 | 2 | Image results | Gallery image filenames and alts (hub carries most of this) |
| i voted stickers by state | 50 | no data | | Hub "All States" list; each state H1 |
| i voted sticker 2026 | 30 | no data | | Title tag year |
| i voted sticker profile picture / i voted profile picture / vote sticker maker / i voted sticker maker / digital i voted sticker | 0 or no data | | Ahrefs has no SERP for these | Lead paragraph ("fits the profile-picture circle"), maker H2. Volume is real in election week (King County and WA SOS publish "digital stickers" for it) but unmeasured |
| yo voté sticker / sticker yo vote / calcomanía yo voté | 20 / 0 / 0 | | | /es/ pages, maker Spanish headlines |

What the sticker SERP wants: images (every top result carries an image thumbnail), a tool or a download,
and news about the state's official sticker. Our pages have the first two; Section 5.10 adds the third
where a state has a contest or a famous design.

### 2b. Voting-Information Queries

Texas (early in-person family):

| Query | Volume | KD | Who ranks | Page element |
|---|---|---|---|---|
| early voting texas / texas early voting | 6,400 / 3,300 | 59 / 37 | votetexas.gov (3 pages), news carousel, vote.org calendar, CBS, sos.state.tx.us, vote411, Dallas County | Title, H1, ribbon, Section 5.4 table, 5.5 |
| texas early voting 2026 / texas early voting dates 2026 | 4,400 / 250 | no data | same | Title year, 5.4 |
| when is early voting in texas / when does early voting start in texas | 1,600 / 1,100 | 61 / 48 | same, plus knowledge panel | 5.4 table row 4, FAQ Q6 |
| voting in texas / how to vote in texas | 3,100 / 150 | 68 / 59 | votetexas.gov, vote.org, knowledge panel, video | 5.5 "How Voting Works" |
| where do i vote in texas | 150 | 62 | votetexas.gov locator, news | 5.6 "Where to Vote", FAQ Q7 |
| texas voter id | 200 | 0 | SOS PDF form, texasvoterprotection.org (DR 24), KVUE, Instagram | 5.7 "Voter ID", FAQ Q2. Weak SERP, best near-term voting-info win |
| texas election day 2026 | 150 | no data | | 5.4 row 6 |
| texas sample ballot 2026 | 500 | no data | | Not targeted (we do not publish ballots); link to the official sample-ballot tool in 5.6 |
| early voting hours texas / texas polling hours / texas election day hours / texas vote by mail deadline | 50 / 30 / 0 / 0 | | | 5.4 rows 4 to 6, 5.8, FAQ Q1 and Q5 |
| early voting near me / where to vote near me | 2,900 / 3,000 | 41 / 73 | Google local results, state locators | Not winnable from a state page; 5.6 links the official locator |

Washington (all-mail family): Ahrefs reports 0 for "washington ballot deadline", "when are ballots due in
washington", "washington state ballot drop box", "where is my ballot washington", "early voting washington
state" and "washington state election 2026". Measured: "washington voter registration" 200 (KD 37),
"washington state voting" 70 (KD 24). The "washington ballot deadline" SERP is sos.wa.gov in 9 of 10
positions with a featured snippet; the People Also Ask box asks "Is Washington state vote by mail only?",
"How late can you vote?", "Can you vote in person if you have a mail in ballot?". Those four questions
are the all-mail FAQ set in Section 5.11. All-mail states are a sticker-first play; the voting facts are
for citation and completeness, not head-term ranking.

Alabama (no early voting family): "voting in alabama" 100 (KD 26), "alabama voter registration" 100,
"alabama sample ballot 2026" 450, "alabama voter id" 40 (KD 2), "early voting alabama" 10, "alabama
absentee ballot" 10 (KD 0, knowledge panel). SERP for "alabama early voting": sos.alabama.gov absentee
page, vote411, Rock the Vote, US Vote Foundation, democracy.works, AARP (still titled 2024). Thin and
partly stale; a dated absentee section (5.8) with the excuse list, witness or notary rule and the noon
Election Day receipt deadline can place.

Per-state "early voting [state]" volume, for the priority order in Section 8: GA 2,600, VA 2,400, FL
2,200, NJ 1,600, OH 1,100, MI 700, IN 500, SC 450, WI 450, MD 400, NY 350, TN 350, LA 300, MN 200, AZ
150, OK 150, MA 150, CA 100, IL 100, IA 90, NC 80 (its parent topic "early voting times and locations" is
far larger), NV 50, KY 30, MO 30, NM 30, KS 20, PA 20, AL 10, CO 10, MS 10, HI 0, NH 0, OR 0, UT 0, WA 0.
Texas is 6,400 on "early voting texas".

Cross-state questions with their own volume that every page answers in one sentence: "can you take a
picture of your ballot" 60 (KD 10), "voter id laws by state" 300 (KD 67), "polling hours" 300 (KD 40).

## 3. Competitor Findings

### Sticker Cluster

| Who ranks | What the page has | What beats it | What cannot be beaten |
|---|---|---|---|
| County and state election offices (Sedgwick County KS, Michigan SOS contest, Williamson County TX) | Contest rules, winning designs, news | Nothing on ranking for the contest itself. Mention the contest with a link in 5.10 so the page is relevant to "[state] i voted sticker" | Their news cycle and .gov authority |
| Amazon, LabelValue, Election Source, BayTech Label | Rolls of physical stickers | A free digital sticker sized for a profile picture, state-specific, with the state's dates. Say "free" and "profile picture" in the title or lead | Shopping intent for physical rolls |
| FVAP.gov "I Voted From" sticker | A downloadable sticker for overseas voters | 51 state-specific sets plus a maker | |
| Wikipedia "I Voted sticker" | History (1950 Miami Herald, 1982 Fort Lauderdale, 1985 Phoenix realtors, 1986 to 1988 national spread) | A short state-tradition paragraph (5.10) where a source exists; the hub can carry the national history | Encyclopedic authority |
| Kapwing template, PicsArt, King County and WA SOS digital stickers | A generic editable sticker or one fixed PNG | State dates on the sticker, 18 designs, Spanish, no upload | |
| Etsy | Returned 403 to WebFetch; not read. Listings are physical and digital downloads for sale | Free, no account | |

### Voting-Information Cluster

| Who ranks | What the page has | What beats it | What cannot be beaten |
|---|---|---|---|
| State SOS or election office (votetexas.gov DR 77, sos.state.tx.us DR 90, sos.wa.gov DR 91, sos.alabama.gov DR 88) | The rules, often without calendar dates (votetexas.gov early voting page: about 200 words, no dates, no hours). The locator tools | One page with the dates, hours, ID list, mail deadlines, selfie rule and campaign-gear rule together, each sourced to that office. Plain answers first | Authority, the locator, the sample ballot, freshness on rule changes. Always link to them |
| Vote.org (DR 82) state pages and early voting calendar | About 1,200 words per state of relative rules ("17 days before Election Day"), tools, no 2026 dates, no provisional or curing info, no last-updated date | Calendar dates and a visible checked-on date for every fact | Backlinks and brand |
| Vote411 (DR 79), Rock the Vote, US Vote Foundation, democracy.works, AARP | Aggregated rules, some pages still titled 2024 | 2026 dates and the checked-on date | |
| News (CBS Texas DR 92, 650 to 750 words, updated October 20; local TV; Texas Tribune) | Dates, state minimum hours by day, ID list, what is on the ballot, county links | Everything they have plus mail, selfie, gear, provisional, cure, Spanish. Update daily during early voting as they do | The news carousel and video slots |
| NCSL early in-person voting brief | A 51-row table of statutory start and end rules with citations, updated August 2026, no 2026 dates | Dates. Use NCSL only as a pointer to the statute, never as the source shown on the page | |
| Ballotpedia "Voting in [State]" | WebFetch returned an empty body; not read | | |

## 4. What a Page Needs to Rank for Both Clusters

1. State name plus "I voted sticker" plus the voting-info family term in the title, H1 and first 100
   words, in that order (sticker first; it is what the page is).
2. Stickers and the maker above the fold; voting facts below them. A voter who came for the dates gets the
   ribbon and a jump link in the hero.
3. Every fact as a short sentence with the answer first, a calendar date, a source link, and a checked-on
   date. That is what gets quoted in AI Overviews and featured snippets.
4. One table of dates (Section 5.4): snippets and knowledge panels pull from tables.
5. FAQPage JSON-LD for the state questions. Google limits FAQ rich results to government and health
   sites since August 2023, so expect no rich result; the schema is for Bing, AI crawlers and parity.
6. Image alts that say "[State] I voted sticker" plus what the sticker says.
7. Internal links already in place (neighbors, same rule, previous/next, all states, Spanish pair), plus
   a new same-rule list for voter ID.
8. Daily rebuilds once early voting windows start closing (October 27 on), so no page says a window is
   open after it closes.

## 5. The Page Template, Section by Section

Order on the page. Sections marked EXISTS are rendered today; NEW sections are added to `site/index.php`
between the maker and the related-state links. Every NEW section is rendered server-side by PHP from the
state's facts file (Section 7) and is omitted entirely when its facts are missing. No section ever renders
placeholder or generic text in place of a missing fact.

| # | Section | Status | Target length | Data source |
|---|---|---|---|---|
| 5.1 | Hero: H1, ribbon, lead, jump buttons, official link, share | EXISTS | 60 to 90 words | `states.js` via `vote_meta()` |
| 5.2 | Ready-Made [State] Stickers gallery | EXISTS | 20 words + images | `site/assets/states/<abbr>/` |
| 5.3 | Or Make Your Own (the maker) | EXISTS | UI | `app.js` |
| 5.4 | [State] Voting Dates and Deadlines, 2026 (table) | NEW | 6 to 8 rows + 60 to 120 words | facts file `dates.*` |
| 5.5 | How Voting Works in [State] (replaces "Before You Vote in [State]") | EXISTS, expanded | 150 to 250 words | `states.js` summary and note + facts `how.*` |
| 5.6 | Where to Vote in [State] | NEW | 80 to 140 words | facts `where.*` |
| 5.7 | Voter ID in [State] | NEW | 90 to 160 words | facts `id.*` |
| 5.8 | Voting by Mail or Absentee in [State] | NEW | 100 to 200 words | facts `mail.*` |
| 5.9 | At the Polls in [State]: Selfies, Stickers and Campaign Gear | NEW | 100 to 180 words | facts `polls_rules.*` |
| 5.10 | [State] and the I Voted Sticker | NEW, optional | 60 to 150 words | facts `sticker.*` |
| 5.11 | Common Questions (FAQ with FAQPage schema) | EXISTS, expanded | 8 to 10 state questions, 25 to 60 words each | FAQ answers in the facts file |
| 5.12 | Related states, previous/next, all states, finder | EXISTS | links | `VOTE_NEIGHBORS`, `states.js` |
| 5.13 | Sources for This Page | NEW | list | every `src` in the facts file, deduplicated |

Page total: 1,500 to 2,200 words of body text, excluding the maker UI, link lists and the footer.

### 5.1 Hero (EXISTS)

Keep `vote_meta()` families (mail, early, eday, unchecked). Two changes:

- Add a third jump button: "Dates and deadlines ↓" to `#dates-h`.
- Add a date-aware fourth family, `early_over`: when today's date (at build time) is after `end` and
  before or on November 3, the ribbon reads "EARLY VOTING HAS ENDED. VOTE ON ELECTION DAY, NOV 3" and the
  lead says "Early voting in [State] ended [end long date]. Election Day is Tuesday, November 3; polls are
  open [poll hours]." The sticker default switches to the Election Day headline (`app.js stateHeadline()`
  already does this when `hasEarly` is false; pass the family in `VOTE_META` so it matches). After
  November 3, every family's ribbon reads "ELECTION DAY WAS NOV 3" and the lead points to the I voted
  sticker.

### 5.2 Gallery (EXISTS)

Change the alt pattern only (Section 6). Keep the download names.

### 5.3 Maker (EXISTS)

No change.

### 5.4 [State] Voting Dates and Deadlines, 2026 (NEW)

An HTML `<table>` with a caption, two columns (What, When), one row per fact present. Rows, in this
order, each omitted when its fact is missing:

| Row | Fact key | Must come from | Never guessed |
|---|---|---|---|
| Voter registration deadline | `dates.reg_deadline` | State election office or statute | Yes. Includes whether same-day registration exists |
| Request a mail or absentee ballot by | `dates.mail_request` | State election office | Yes |
| Return your mail ballot by | `dates.mail_return` | State election office | Yes. Must say postmark or received |
| Early voting | `dates.early_window` from `states.js` start, end, dateLine | `states.js` (already verified) | Yes |
| Early voting hours | `dates.early_hours` | State election office or statute (for example Tex. Elec. Code 85.005) | Yes. "Vary by county" is an acceptable answer when the source says so |
| Election Day polls | `dates.polls` from the FAQ `polls` answer | Already in `faq-states.php` | Yes |
| Last day to fix (cure) a mail ballot problem | `dates.cure` | State election office or statute | Yes. Omit when the state has no cure process or none was found |

Under the table, one paragraph of 60 to 120 words that says where the dates come from and the one most
important thing for this family (early: "any early voting site in your county" or "your assigned site";
mail: "received by" or "postmarked by"; none: the absentee excuse rule). Then the line: "Dates checked
[checked date] at [source host]."

### 5.5 How Voting Works in [State] (EXISTS, expanded)

Keep the current `vote_info()` sentences (summary or standard first sentence, Election Day line, the
`note`, the official link card, the source line). Add two paragraphs from the facts file:

- `how.in_person`: how in-person voting works for this family: any site in the county or an assigned
  precinct; vote centers; what to bring. 50 to 90 words.
- `how.first_time`: what a first-time or recently moved voter should check (registration status link).
  30 to 60 words.

### 5.6 Where to Vote in [State] (NEW)

- `where.locator_url` and `where.locator_label`: the official polling place or drop box locator. Rendered
  as a card like the existing official link. Required for the section to render.
- `where.rule`: one sentence: countywide vote centers, assigned precinct, or both (Election Day may
  differ from early voting). 20 to 50 words.
- `where.local_offices_url`: the state's list of county or town election offices, when it exists.
- `where.sample_ballot_url`: the official sample ballot tool, when it exists ("texas sample ballot 2026"
  has 500 searches a month; we answer it with the link, never the ballot).

### 5.7 Voter ID in [State] (NEW)

- First sentence: the existing FAQ `id` answer.
- `id.accepted`: an HTML list of accepted IDs, copied from the official list (3 to 10 items, official
  wording shortened, no additions).
- `id.without`: what happens without ID (affidavit, provisional, return within N days). 20 to 60 words.
- `id.expired`: whether expired IDs count, only when the source says.
- `id.rule`: one of `photo_required`, `photo_requested`, `id_nonphoto`, `no_id` (the classes already
  used in `docs/faq-sources.json`). Drives the "Other States With the Same ID Rule" link list (5.12).

### 5.8 Voting by Mail or Absentee in [State] (NEW)

- `mail.who`: who can vote by mail: everyone (all-mail), no-excuse, or the excuse list copied from the
  official source. 30 to 100 words.
- `mail.request`, `mail.return`: deadlines (same values as the table, rendered as sentences).
- `mail.witness`: notary or witness requirements, only when the source states them (Alabama: notarized
  or two adult witnesses).
- `mail.track_url`: the official ballot tracking tool, when it exists.
- `mail.cure`: how a voter fixes a signature or ID problem and by when. Omit when not found.
- `mail.dropbox`: whether official drop boxes exist and the locator link.

For all-mail states this section comes before 5.6 and 5.7 in the rendered order (mail is how the state
votes). The template checks `kind === 'mail'`.

### 5.9 At the Polls in [State]: Selfies, Stickers and Campaign Gear (NEW)

- `polls_rules.selfie`: the existing FAQ `selfie` answer. When the FAQ has none, this paragraph is
  omitted and the FAQ question is not rendered.
- `polls_rules.gear`: whether a voter can wear campaign clothing or buttons inside or near the polls, the
  distance (Texas: 100 feet, only for candidates, parties or measures on the current ballot), and the
  source. Never guessed; omit when no statute or election office page says it. Note that a nonpartisan
  "I voted" sticker is not campaign material anywhere this plan checked, but write that only when the
  source addresses it.
- `polls_rules.phone`: whether phones can be used in the polling place, only when the source says.
- `polls_rules.line`: "If you are in line at closing, you can vote", only when the source says.

### 5.10 [State] and the I Voted Sticker (NEW, optional)

Render only when `sticker.tradition` is present with a source. Content: the state's official sticker
contest (Michigan SOS, Georgia's peach, Ulster County NY), a county contest (Travis, Williamson and Dallas
counties in Texas), or a design the state hands out, in 60 to 150 words, with the link. Then one fixed
sentence that is the same on every page: "Whether or not your polling place hands one out, you can save a
free [State] I voted sticker above and set it as your profile picture." That sentence is the bridge to
the sticker cluster and is allowed to repeat across pages. The national history (1950 Miami Herald, 1985
Phoenix) belongs on the hub, not on state pages.

### 5.11 Common Questions (EXISTS, expanded)

Order and question text, fixed. Each is rendered only when its answer is in the facts file with a source.
Answers are 25 to 60 words, answer first.

1. When are polls open in [State] on Election Day? (exists)
2. Do I need ID to vote in [State]? (exists)
3. Can I still register to vote in [State]? (exists; after the deadline the answer starts "No, not for
   the November 3 election" or describes same-day registration)
4. Can I take a picture of my ballot in [State]? (exists)
5. When is the ballot deadline in [State]? (exists for mail states; add to every state that offers
   absentee voting)
6. When does early voting start and end in [State]? (new; families early and early_over)
7. Where do I vote early in [State]? (new; locator link, county rule)
8. Can I wear a campaign shirt or button to the polls in [State]? (new, from 5.9)
9. What is a provisional ballot in [State], and does it count? (new; `faq.provisional`)
10. How do I check that my mail ballot was counted in [State]? (new; `faq.track`)
11. What if I am in line when the polls close in [State]? (new; `faq.line`)

All-mail states add: Is [State] vote by mail only? Can I vote in person in [State] if I got a mail
ballot? (the PAA questions on the Washington SERP).

The 7 generic questions stay after the state questions. The state questions get FAQPage JSON-LD; the
generic ones do not.

### 5.12 Related States (EXISTS, one addition)

Add a third list after "States Next Door": "Other States With the Same Voter ID Rule", built from
`id.rule` across the facts files, labelled by the rule in plain words ("photo ID required", "ID
requested, affidavit allowed", "no ID for most voters"). Up to 8 links, alphabetical. QA checks both
directions as it does for neighbors.

### 5.13 Sources for This Page (NEW)

A `<details>` block titled "Sources for this page" listing every distinct `src` URL used on the page with
its host and the checked-on date, newest first. One line each.

## 6. Title, H1, Meta, H2, Schema, Links and Alts

### Title Tag (65 characters max, QA enforced)

Keep the current formula's head and swap the tail to name the voting-info family. Patterns, with
`vote_name()` for the state:

| Family | Pattern | Example |
|---|---|---|
| early | `[State] I Voted Stickers: Early Voting [range] and Dates` | Texas I Voted Stickers: Early Voting Oct 19–30 and Dates |
| early (dateLine) | `[State] I Voted Stickers: Early Voting Through [end]` | Idaho I Voted Stickers: Early Voting Through Oct 30 |
| early_over | `[State] I Voted Stickers: Election Day Nov 3, Hours and ID` | |
| mail | `[State] I Voted Stickers: Ballot Due Nov 3, Vote by Mail` | |
| none | `[State] I Voted Stickers: Election Day Nov 3, Absentee Rules` | |
| unchecked | current unchecked title, unchanged | |

If a pattern exceeds 65 characters for a long state name, drop "and Dates" or "and ID" first, then the
range. The en dash in ranges is the site's existing convention (`vote_range()`); never an em dash.

### H1

`[State] I Voted Stickers and 2026 Voting Guide` for every family. The ribbon under it carries the
family fact as it does today.

Spanish: `Stickers para votar en [State] y guía para votar 2026` (sentence case per
`docs/spanish-style.md`).

### Meta Description (70 to 230 characters, QA enforced)

`[fact sentence from vote_meta()] Free I voted stickers sized for your profile picture, plus [State]
voter ID, mail ballot deadlines, polling hours and where to vote, from official sources checked [checked
short date].` Trim the list to fit 230.

### H2 Patterns (English; Spanish in Section 9)

Exact strings, so the Spanish dictionary keys match:

- `Ready-Made [State] Stickers` (exists)
- `Or Make Your Own` (exists)
- `[State] Voting Dates and Deadlines, 2026`
- `How Voting Works in [State]`
- `Where to Vote in [State]`
- `Voter ID in [State]`
- `Voting by Mail or Absentee in [State]`
- `At the Polls in [State]: Selfies, Stickers and Campaign Gear`
- `[State] and the I Voted Sticker`
- `Common Questions` (exists)
- `Other States With the Same Voter ID Rule`
- `Sources for This Page`

DC uses "DC" in English headings as the existing code does.

### Schema

Add a second JSON-LD block on state pages only:

```json
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[
 {"@type":"Question","name":"When are polls open in Texas on Election Day?",
  "acceptedAnswer":{"@type":"Answer","text":"Polls are open 7 a.m. to 7 p.m. local time. Texas has two time zones."}}
]}
```

Built from the same array that renders the visible state questions, so text and schema cannot drift.
Keep BreadcrumbList. Do not add Event, GovernmentService or Dataset types; the page is not the event
organizer.

### Internal Linking

Exists: hub breadcrumb, same-rule states, neighbors (QA checks both ways), previous/next chain, all 51
states, Spanish pair via hreflang and the language switch. Add: the voter ID same-rule list (5.12), and
in 5.6 one link to each neighbor state's "Where to Vote" anchor only when the state shares a metro with a
neighbor (DC to Maryland and Virginia; New Jersey to New York and Pennsylvania; Kansas to Missouri). Those
pairs are listed in the facts file as `where.metro_neighbors` and are the only in-prose cross-links.

### Image Alt Pattern

Gallery images: `[State] I voted sticker, design [N]: [first line] [second line]` where the two lines are
the sticker's printed text from `docs/codex-prompts/NN-xx-state.md` (slot list) or, for the October 19
and 20 sets, read from the image. Example: `Alabama I voted sticker, design 6: I VOTED, ALABAMA`. The
hero sticker stays `alt=""` (decorative; the H1 says the same thing). Spanish pages draw their own art
and keep `alt=""`.

## 7. Per-State Research Checklist and Data File

### Where the Data Lives

One JSON file per state at `build/state-facts/<abbr>.json` (lowercase abbr), read by a new
`build/facts_text.py` that writes `site/facts-states.php` the way `build/faq_text.py` writes
`site/faq-states.php`. `site/index.php` loads it with a `vote_state_facts( $st )` function modelled on
`vote_state_faq()`. The build fails if a file is missing or invalid. `states.js` stays the only file for
early voting dates; the facts file never repeats `start`, `end`, `officialUrl` or `checked`.

### Rules for Every Fact

Each fact is an object: `{"en": "...", "es": "...", "src": "https://...", "checked": "2026-10-22",
"quote": "...", "status": "verified"}`.

- `src` must be an official source: the state election office or Secretary of State domain, the state
  legislature or code site, or a county or municipal election office when the state has no statewide
  page (Georgia's calendar via Liberty County is the existing precedent; note it in `notes`). Never
  Vote.org, Vote411, Ballotpedia, NCSL, Wikipedia, news or AI answers. Those may lead you to the official
  page; the official page is what you cite.
- `quote` is the sentence from the source that supports `en`, copied, not paraphrased. Required.
- `checked` is the date you read the page, YYYY-MM-DD, within the last 14 days at build time.
- `status` is `verified` or `missing`. A `missing` fact has no other fields and the section or row is
  omitted. There is no `estimated`, `likely` or `draft` status.
- `en` is 1 to 3 sentences, answer first, exact dates in "Month D, YYYY" form, times as "7 a.m.", no em
  dashes, no advice beyond the source, "may" and "can" kept conditional as in the source.
- `es` follows `docs/spanish-style.md`. It can be left `""` and filled in Phase 3; the English page
  renders without it, and the Spanish page omits the fact until it is filled (QA flags the gap).
- If a .gov page returns 403 to WebFetch or curl, read it in Chrome (Claude in Chrome tools). Do not use
  Firecrawl.

### Never Guessed, Ever

Dates and deadlines, hours, the accepted-ID list, what happens without ID, ballot selfie legality,
electioneering distance and clothing rules, cure deadlines, witness or notary rules, whether same-day
registration exists, drop box existence. When the source does not answer, write `"status": "missing"`.
The New Hampshire selfie entry in `docs/faq-sources.json` is the model: statute says one thing, a court
said another, so the page says nothing.

### The Checklist (do in this order, stop at each if the official page answers it)

1. Open the state's `officialUrl` and `sourceUrl` from `states.js`. Note the election office's domain;
   that is your allowed source host, plus the legislature site.
2. Registration: deadline date, same-day registration (yes, during early voting only, Election Day, no),
   status-check URL. Fills `dates.reg_deadline`, `how.first_time`.
3. Early voting hours rule: statewide hours, minimums, or "set by county". Fills `dates.early_hours`.
4. Where: locator URL and label, county or precinct rule for early voting and for Election Day, county
   offices list URL, sample ballot URL. Fills `where.*`.
5. ID: the accepted list, the no-ID path, expired-ID rule. Fills `id.*`, `dates` none.
6. Mail or absentee: who qualifies, request deadline (received or postmarked), return deadline
   (received or postmarked, time), drop boxes, tracking URL, cure process and deadline, witness or
   notary. Fills `mail.*`, `dates.mail_request`, `dates.mail_return`, `dates.cure`.
7. Polls rules: electioneering distance and clothing rule, phone use, in-line-at-closing rule. Fills
   `polls_rules.*`, FAQ 8 and 11.
8. Provisional ballot: when it is issued and when it counts. Fills FAQ 9.
9. Sticker tradition: search "[state] i voted sticker contest" and "[state] secretary of state i voted
   sticker". Official page only (SOS or county). Fills `sticker.tradition` or `missing`.
10. Write the file. Run `python3 -I build/facts_text.py --check <abbr>` (Section 8.3). Fix every error.
    Then `python3 -I build/facts_text.py` to regenerate `site/facts-states.php`.

### The File Format

```json
{
 "abbr": "TX",
 "dates": {
  "reg_deadline": {"en": "...", "es": "", "src": "...", "checked": "2026-10-22", "quote": "...", "status": "verified"},
  "mail_request": {},
  "mail_return": {},
  "early_hours": {},
  "cure": {"status": "missing"}
 },
 "how": {"in_person": {}, "first_time": {}},
 "where": {"locator_url": "", "locator_label": "", "rule": {}, "local_offices_url": "", "sample_ballot_url": "", "metro_neighbors": []},
 "id": {"rule": "photo_required", "accepted": {"en": ["...", "..."], "es": [], "src": "", "checked": "", "quote": "", "status": "verified"}, "without": {}, "expired": {}},
 "mail": {"who": {}, "witness": {}, "track_url": "", "cure": {}, "dropbox": {}},
 "polls_rules": {"gear": {}, "phone": {}, "line": {}},
 "sticker": {"tradition": {}},
 "faq": {"early_window": {}, "where_early": {}, "gear": {}, "provisional": {}, "track": {}, "line": {}, "mail_only": {}, "in_person_mail_state": {}},
 "notes": "free text: what was not found and where you looked"
}
```

`dates.polls`, `id` first sentence, the selfie answer and the registration FAQ are not repeated here; the
template reads them from `faq-states.php`. `faq.*` entries hold the answers to the new questions in
Section 5.11; the question text is fixed in the template.

### Worked Example: Texas

Values below were read for this plan on October 21, 2026 from the sources named. Items marked RE-VERIFY
came from a non-official page (CBS, Vote.org) and must be confirmed on the official page before the
file is saved; if the official page does not confirm them, they become `missing`.

```json
{
 "abbr": "TX",
 "dates": {
  "reg_deadline": {"en": "The deadline was October 5, 2026. Texas has no same-day or Election Day registration.", "es": "", "src": "https://www.sos.state.tx.us/elections/voter/important-election-dates.shtml", "checked": "2026-10-21", "quote": "Last Day to Register to Vote: Monday, October 5, 2026", "status": "verified"},
  "mail_request": {"en": "RE-VERIFY: your application to vote by mail must be received by your county by October 23, 2026 (the 11th day before Election Day).", "es": "", "src": "https://www.votetexas.gov/voting-by-mail/index.html", "checked": "", "quote": "", "status": "verified"},
  "mail_return": {"en": "RE-VERIFY: a mailed ballot must be postmarked by 7 p.m. on November 3 and received by 5 p.m. on November 4, 2026.", "es": "", "src": "https://www.votetexas.gov/voting-by-mail/index.html", "checked": "", "quote": "", "status": "verified"},
  "early_hours": {"en": "RE-VERIFY against Tex. Elec. Code 85.005: counties must offer at least 9 hours a day October 19 to 23, 12 hours on Saturday, October 24, 6 hours on Sunday, October 25, and 12 hours a day October 26 to 30. Exact hours are set by each county.", "es": "", "src": "https://statutes.capitol.texas.gov/Docs/EL/htm/EL.85.htm", "checked": "", "quote": "", "status": "verified"},
  "cure": {"status": "missing", "notes": "Texas has a ballot tracker with a cure path for ID numbers (SB 1); find the deadline on votetexas.gov or 'Track My Ballot' before publishing."}
 },
 "how": {
  "in_person": {"en": "During early voting you can vote at any early voting location in the county where you are registered. On Election Day some counties use countywide vote centers and others require your assigned precinct; the official locator tells you which.", "es": "", "src": "https://www.votetexas.gov/voting/where.html", "checked": "2026-10-21", "quote": "", "status": "verified"},
  "first_time": {"en": "Check that you are registered and that your address is current at the Secretary of State's Am I Registered tool before you go.", "es": "", "src": "https://teamrv-mvp.sos.texas.gov/MVP/mvp.do", "checked": "2026-10-21", "quote": "", "status": "verified"}
 },
 "where": {"locator_url": "https://www.votetexas.gov/voting/where.html", "locator_label": "VoteTexas.gov polling locations", "rule": {"en": "Any early voting site in your county; your assigned polling place or a countywide vote center on Election Day, depending on your county.", "es": "", "src": "https://www.votetexas.gov/voting/where.html", "checked": "2026-10-21", "quote": "", "status": "verified"}, "local_offices_url": "https://www.sos.state.tx.us/elections/voter/county.shtml", "sample_ballot_url": "", "metro_neighbors": []},
 "id": {"rule": "photo_required",
  "accepted": {"en": ["Texas driver license", "Texas Election Identification Certificate", "Texas personal identification card", "Texas handgun license", "U.S. military ID with photo", "U.S. citizenship certificate with photo", "U.S. passport (book or card)"], "es": [], "src": "https://www.votetexas.gov/register-to-vote/need-id.html", "checked": "2026-10-21", "quote": "", "status": "verified"},
  "without": {"en": "If you cannot reasonably get one of the seven IDs, sign a Reasonable Impediment Declaration and show a supporting document such as your voter registration certificate, a utility bill or a bank statement.", "es": "", "src": "https://www.votetexas.gov/register-to-vote/need-id.html", "checked": "2026-10-21", "quote": "", "status": "verified"},
  "expired": {"en": "RE-VERIFY: for voters 18 to 69 the ID may be expired up to 4 years; voters 70 and older may use an expired ID of any age.", "es": "", "src": "https://www.votetexas.gov/register-to-vote/need-id.html", "checked": "", "quote": "", "status": "verified"}
 },
 "mail": {
  "who": {"en": "RE-VERIFY: you can vote by mail if you are 65 or older, sick or disabled, expecting to give birth within three weeks of Election Day, out of your county during early voting and on Election Day, or in jail but eligible to vote.", "es": "", "src": "https://www.votetexas.gov/voting-by-mail/index.html", "checked": "", "quote": "", "status": "verified"},
  "witness": {"status": "missing"},
  "track_url": "https://teamrv-mvp.sos.texas.gov/BallotTrackerApp/#/login",
  "cure": {"status": "missing"},
  "dropbox": {"en": "RE-VERIFY: Texas allows hand delivery of your own mail ballot to your county's early voting clerk's office while polls are open on Election Day, with ID.", "es": "", "src": "", "checked": "", "quote": "", "status": "verified"}
 },
 "polls_rules": {
  "gear": {"en": "RE-VERIFY against Tex. Elec. Code 61.003 and 85.036: no electioneering within 100 feet of the polling place entrance, including clothing, buttons or signs for a candidate, party or measure on the current ballot. A nonpartisan I voted sticker is not campaign material.", "es": "", "src": "https://statutes.capitol.texas.gov/Docs/EL/htm/EL.61.htm", "checked": "", "quote": "", "status": "verified"},
  "phone": {"en": "Texas bars using a wireless device within 100 feet of a voting station.", "es": "", "src": "https://statutes.capitol.texas.gov/Docs/EL/htm/EL.61.htm", "checked": "", "quote": "", "status": "verified"},
  "line": {"status": "missing"}
 },
 "sticker": {"tradition": {"en": "RE-VERIFY on county pages: Texas has no statewide sticker. Counties run their own designs and contests: Travis County's 2022 bluebonnet sticker by a UT student was printed 100,000 times in English and Spanish; Williamson County and Dallas County hold student design contests.", "es": "", "src": "https://www.wilcotx.gov/Sticker-Contest", "checked": "2026-10-21", "quote": "", "status": "verified"}},
 "faq": {
  "early_window": {"en": "Early voting runs Monday, October 19 to Friday, October 30, 2026, at any early voting site in your county. Hours are set by the county within state minimums.", "es": "", "src": "https://www.sos.state.tx.us/elections/voter/important-election-dates.shtml", "checked": "2026-10-19", "quote": "", "status": "verified"},
  "where_early": {"en": "Any early voting location in the county where you are registered. Find them at VoteTexas.gov.", "es": "", "src": "https://www.votetexas.gov/voting/where.html", "checked": "2026-10-21", "quote": "", "status": "verified"},
  "gear": {"status": "missing"},
  "provisional": {"status": "missing"},
  "track": {"status": "missing"},
  "line": {"status": "missing"}
 },
 "notes": "votetexas.gov/voting/when.html redirects; use voting/index.html and early-voting.html. Hours minimums and mail eligibility were read on CBS and Vote.org and must be confirmed at votetexas.gov or the Election Code before the file is accepted. Replace every RE-VERIFY and empty checked/quote, or set the fact to missing."
}
```

The validator in Section 8.3 rejects this file as written (empty `quote`, empty `checked`, "RE-VERIFY" in
`en`); that is the point. The implementer finishes it against official pages until it passes.

## 8. Implementation Plan

### 8.1 Phases

| Phase | Days | Who | Work | Done when |
|---|---|---|---|---|
| 0. Template and tooling | Oct 22 (day 1) | Claude with Garrett | `build/facts_text.py` with `--check`; `site/facts-states.php` loader; the NEW sections in `site/index.php` (5.4 to 5.13) rendered only when facts exist; `early_over` family; FAQPage JSON-LD; alt pattern; title, H1 and description patterns; `scripts/qa_facts.py`; additions to `qa_seo.py` and `qa_es.py`; Spanish dictionary keys for every new fixed string. Texas facts file finished and verified. Build, QA, deploy Texas | All QA passes with Texas full and 50 states unchanged (their pages render exactly as today because their facts files are all `missing`) |
| 1. Tier 1 states | Oct 23 to 25 | Smaller model, 7 states a day | Facts files for the 20 Tier 1 states in Section 8.4 order. Deploy once a day after QA | 21 states full |
| 2. Tier 2 and the 21 unchecked dates | Oct 26 to 29 | Smaller model, 8 states a day | First, for each unchecked state in `states.js`: read the official early voting page in Chrome, fill `start`, `end`, `kind`, `varies`, `officialUrl`, `sourceUrl`, `checked`, set `status` to `verified`, run `build/state_pages.py`. Then the facts file | 51 states full |
| 3. Spanish and the closing window | Oct 27 to Nov 3 | Smaller model for translation; Garrett reviews | Fill every `es` in Tier 1 then Tier 2 order (Section 9). Daily rebuild and deploy at 6 a.m. Central from Oct 27 so `early_over` flips the day after each state's `end`. Nov 4: post-election ribbon | Every page in both languages passes `qa_es.py`; no page says a closed window is open |

Daily during Phases 1 to 3: `python3 -I build/build_static.py`, `python3 -I scripts/qa_seo.py --base
http://127.0.0.1:8792/ --no-legacy`, `python3 -I scripts/qa_facts.py`, `node scripts/qa_cards.mjs --base
http://127.0.0.1:8792/`, deploy with wrangler, `python3 -I scripts/qa_seo.py`, `python3 -I
scripts/qa_es.py`, `python3 -I scripts/indexnow.py`. Commit `build/lastmod.json` and
`build/indexnow-sent.json` with each build.

### 8.2 The Prompt the Smaller Model Runs Per State

Paste this with the two placeholders filled. It assumes Phase 0 is done.

```
You are filling the voting facts file for one state on votesticker.org. State: [STATE NAME] ([ABBR]).
Repo: /Users/gnafzing/Documents/seo-command-center/clients/hobby-sites/votesticker

Voice and rules: docs/brand-brief.md (read it first). Write each fact the way a friendly neighbor who
checked the facts would say it: lead with what the voter does ("Leave the campaign shirts and hats at
home."), then the rule, then the next step. Lists for eligibility conditions (mail.who is the intro
sentence, mail.who_list the items). A passed deadline says "Passed (date)" and the page adds the next
step. Add a "sources" map naming every host you cite, in English and Spanish ("the Texas Secretary of
State" / "la Secretaría de Estado de Texas"); the page shows one source line per section from it.
Texas (build/state-facts/tx.json) and Washington (wa.json) are the models.

Read first, in this order: docs/content-strategy-state-pages.md Section 7 (rules, checklist, file
format), the row for [ABBR] in site/states.js, the [ABBR] block in site/faq-states.php, and the [ABBR]
entry in docs/faq-sources.json (it has notes on what was and was not found on October 19).

Then work through the checklist in Section 7, step by step. Sources: only the state election office or
Secretary of State site, the state legislature or code site, or a county election office when no state
page exists. If a page returns 403, open it with the Claude in Chrome tools. Never use Firecrawl. Never
cite Vote.org, Vote411, Ballotpedia, NCSL, Wikipedia or news on the page; you may read them to find the
official page.

Write build/state-facts/[abbr].json in the exact format in Section 7. For every fact: en (1 to 3
sentences, answer first, exact dates), src (official URL), checked (today, YYYY-MM-DD), quote (the
sentence from the source, copied), status "verified". If the official source does not answer, write
{"status": "missing"} and say in "notes" where you looked. Never estimate, infer or copy a competitor's
number. Leave every "es" as "" unless the state's official site publishes the same fact in Spanish; then
copy that Spanish and cite the Spanish page.

Writing rules: short sentences, no em dashes, no "may be", no advice beyond the source, times as "7 a.m.",
dates as "October 30, 2026". The state's name, never "the state".

Run: python3 -I build/facts_text.py --check [abbr]
Fix every error it prints. Repeat until it prints OK. Then run: python3 -I build/facts_text.py
Do not edit site/index.php, site/states.js, site/faq-states.php or any other file. Do not commit or
deploy. Report: the facts marked missing and why, any fact where two official pages disagree (quote both),
and the final --check output.
```

For Phase 2 unchecked states, prepend: "Before the facts file, verify this state's early voting dates:
read the official early voting page in Chrome, then edit only the [ABBR] line of site/states.js to the
verified format used by Texas (kind, start, end, varies, note, officialUrl, officialLabel, sourceUrl,
checked, status verified; add dateLine and summary only if counties start on different days, like Kansas).
If the official page does not show November 2026 dates, leave the row unchecked and say so. Then run
python3 -I build/state_pages.py."

### 8.3 QA the Implementer Must Pass

Existing, unchanged: `scripts/qa_seo.py` (52 pages: titles 65 max, descriptions 70 to 230, one H1,
canonical, breadcrumb JSON-LD, no em dash in title or description, neighbors both ways, prev/next chain,
sitemap equals indexable pages, og:image loads), `scripts/qa_es.py` (pairs, hreflang, no English left,
structural parity: same count of FAQs, related links, pager and state links), `scripts/qa_cards.mjs`
(every card inside the circle, server panel equals JS panel).

Add to `build/facts_text.py --check <abbr>` (fails the build on any):

- JSON parses; `abbr` matches the filename; every key is in the schema; no unknown keys.
- Every fact with `status: verified` has non-empty `en`, `src`, `checked`, `quote`.
- `src` host is in the allow list: the hosts of `officialUrl` and `sourceUrl` for that state in
  `states.js`, every `src` host already in `faq-states.php` for that state, any host ending `.gov`, and
  the legislature hosts already used in `docs/faq-sources.json`. Everything else fails with the host named.
- `checked` within 14 days of today.
- No em dash or en dash in `en` or `es` (the existing `faq_text.py` assertion); no "RE-VERIFY", "TODO",
  "approximately", "likely", "should be", "probably", "typically"; no year other than 2026 or 2027 in a
  date; dates parse and fall between 2026-09-01 and 2026-11-20.
- `id.rule` is one of the four classes.
- `en` length 60 to 600 characters per fact; `id.accepted` 3 to 10 items.
- Every URL field answers 200 or 403 (403 counts as reachable for .gov sites behind bot protection) with
  a HEAD or GET.

Add `scripts/qa_facts.py` (runs against the built `dist/` or live):

- Each state page's body text (main, minus the maker UI and nav lists) is 1,400 to 2,400 words once its
  facts file has 70 percent or more of facts verified; pages below 70 percent are reported, not failed.
- Duplicate-content check: for each pair of state pages, the 8-word shingle overlap of the NEW sections'
  text (5.4 to 5.10, excluding the fixed sentence in 5.10 and fixed labels) is under 30 percent. Report
  the worst five pairs; fail above 50 percent.
- FAQPage JSON-LD parses; its questions equal the visible state `<details>` summaries in order; answers
  equal the visible text.
- Every visible date on a verified page is not described as upcoming once past: for family
  `early_over`, the ribbon contains "HAS ENDED"; for every family, no "runs [start] to [end]" when today
  is after `end`.
- Every `src` link in "Sources for This Page" appears somewhere above it on the page, and every fact's
  `src` is in the list.
- Spanish parity (extends `qa_es.py`): for each fact with `es` present, the Spanish page renders it; for
  each with `es` empty, the Spanish page omits that fact and the omission is reported with the key, so
  the translation backlog is the QA output.
- Two JSON-LD blocks on state pages, one on the hub.

Add to `qa_seo.py`: titles follow the Section 6 patterns (regex per family); H1 equals the pattern;
description contains the state name and "I voted"; gallery alts start with "[State] I voted sticker".

### 8.4 Prioritized State Order

Two signals: "early voting [state]" volume (Section 2) and days of early voting left (states whose window
closes first get their page first, so the dates section is live while people search for it). Population
breaks ties.

Tier 1, Phase 1, in this order (ends soonest and highest volume first):

1. Texas (worked example; ends Oct 30; 6,400)
2. Georgia (unchecked dates; verify first; 2,600)
3. Virginia (ends Oct 31; 2,400)
4. Florida (ends Oct 31 statewide minimum; 2,200)
5. North Carolina (ends Oct 31; parent topic large)
6. Maryland (ends Oct 29; 400)
7. Pennsylvania (in-person mail ballot ends Oct 27; 20 on "early voting" but 13 million people)
8. Nevada (ends Oct 30; 50)
9. New Jersey (ends Nov 1; 1,600)
10. New York (ends Nov 1; 350; 19 million people)
11. Michigan (ends Nov 1; 700; the one state with a sticker query at 450)
12. Ohio (unchecked; verify first; 1,100)
13. Tennessee (unchecked; verify first; 350, KD 7)
14. Indiana (unchecked; verify first; 500, KD 12)
15. South Carolina (ends Oct 31; 450)
16. Wisconsin (unchecked; verify first; 450)
17. Illinois (unchecked; verify first; 100; 12 million people)
18. California (all-mail; 100; 39 million people; sticker query 50)
19. Massachusetts (unchecked; verify first; 150)
20. Arizona (unchecked; verify first; 150)

Tier 2, Phase 2, alphabetical within group: remaining verified states first (Alaska, Colorado, Delaware,
DC, Hawaii, Idaho, Iowa, Kansas, Minnesota, Mississippi, Missouri, Nebraska, New Hampshire, Oklahoma,
Oregon, Rhode Island, Washington, Alabama), then remaining unchecked (Arkansas, Connecticut, Kentucky,
Louisiana, Maine, Montana, New Mexico, North Dakota, South Dakota, Utah, Vermont, West Virginia,
Wyoming).

Spanish (Phase 3) follows the same order, Texas, California, Florida, New York, Arizona, Illinois, New
Jersey, Georgia and Nevada first because of their Spanish-speaking populations, then the rest of Tier 1.

## 9. Spanish

What gets translated: every `en` fact in the facts files (the `es` field), the fixed H2 strings and FAQ
questions (added to `site/i18n/es.php` under their English keys in Phase 0), the `id.accepted` list
items, the table caption and column heads, and the "Sources for this page" label. Not translated: state
names (English everywhere, per `docs/spanish-style.md`), URLs, official titles (Spanish rendering with the
English in brackets, as the style guide says), quotes (the `quote` field is never shown).

Glossary: `docs/spanish-style.md` is the only glossary. Terms the new sections need that it does not
have yet, proposed for the reviewer and to be added to that file before Phase 3 starts: provisional
ballot (boleta provisional), cure or fix your ballot (corregir tu boleta), ballot tracking (rastrear tu
boleta), electioneering (propaganda electoral), campaign gear (ropa o botones de campaña), vote center
(centro de votación), assigned polling place (lugar de votación asignado), witness (testigo), notarized
(notariada), sample ballot (boleta de muestra), voter ID (identificación para votar), same-day
registration (registro el mismo día).

Register and QA: tú throughout, Spanish sentence-case headings, day-first dates ("30 de octubre de
2026"), times "7 p. m.". `scripts/qa_es.py` already fails on English sentinels and function words left
on a Spanish page; extend its SENTINELS list with the new English H2 strings. The Spanish page omits any
fact whose `es` is empty, and `qa_facts.py` lists those omissions, so the backlog is visible. Every
Spanish fact is a translation of a verified English fact with the same `src`; nothing is researched in
Spanish separately, except when the state publishes the fact in Spanish, in which case the Spanish page
cites the Spanish URL (store it as `src_es`).

## 10. Risks and How the Plan Prevents Each

**Thin or duplicated content across 51 pages.** Today's pages are 80 percent shared template text, which
is why they are 765 words and read alike. The NEW sections render only from state-specific verified facts
and are omitted when missing, so a page with few facts is short rather than padded. The only sentence
allowed to repeat is the one in 5.10. `qa_facts.py` measures 8-word shingle overlap between every pair of
pages' new sections and fails above 50 percent. Family text (ribbon, lead, "How Voting Works" first
sentence) stays templated on purpose; it is 60 to 90 words and is correct for the state.

**Stale dates after early voting ends.** The `early_over` family flips the ribbon, lead, default sticker
headline and title the day after `end`, computed at build time from `states.js`, so a rebuild each
morning from October 27 is required and is in the Phase 3 schedule. `qa_facts.py` fails any page that
says a closed window "runs". After November 3 every page flips to the post-election ribbon. Facts with a
`checked` date older than 14 days fail the build, so nothing verified on October 19 survives to November 3
without a recheck; the recheck is a reread and a date change when nothing moved.

**Legal accuracy.** Only official sources, a copied quote for every fact, no inference (the New Hampshire
selfie precedent), and an explicit never-guessed list. Facts the source does not answer are omitted, not
softened. Registration answers already in the FAQ are fixed dates; when a deadline passes, the answer
starts with "No, not for the November 3 election". Where two official pages disagree, the implementer
reports both quotes and the fact stays `missing` until Garrett picks. The footer and the trust block
already say the election office has the final word; 5.13 lists every source on the page.

**Twelve days and a new domain.** The order in 8.4 puts the pages with the most searches and the soonest
deadlines first, so if the work stops at 20 states the right 20 are done. IndexNow runs after each
deploy; Google Search Console gets the sitemap resubmitted after Phase 1. Expect Google to index and
rerank over 1 to 3 weeks; the sticker queries and the post-election "I voted" switch keep the pages
earning after November 3.

**The smaller model guessing.** The validator rejects empty quotes, stale dates, non-official hosts,
hedging words and placeholder text; the prompt forbids editing any file but the one facts file; nothing
it writes reaches a page until `facts_text.py` regenerates `site/facts-states.php` and QA passes.

## Lessons From the Texas Review (October 21)

Garrett's review of the first Texas page, applied to the template and every state after it:

- Say each date once: ribbon and dates table. The old "Before You Vote" panel is now a summary box
  (dates, hours, ID, where to vote) and the sections below never repeat the dates.
- One source line per section, with named sources and one page-level "last checked" date
  (`vote_sources_line()`), instead of a "Source:" stamp under every paragraph. Facts files name every
  host in `sources`.
- Headings read the way people search: Key Dates for Nov. 3, Where You Can Vote, What ID to Bring,
  Who Can Vote by Mail, Phones, Photos, and Campaign Gear, Questions People Ask.
- Facts lead with what the voter does, then the rule. Eligibility as a list. A passed deadline gets a
  next step. The phone rule carries the sticker angle ("take your sticker selfie outside").
- The maker shows designs before the preview on phones. The all-states list is collapsed on state
  pages and shows each state's key date. Unchecked states read "Election Day, Nov 3", not "not
  checked yet".
- Alt text: "[State] vote sticker, ready-made design N" for now; per-image descriptions are still open.

## What This Plan Could Not Verify

- Ahrefs has no SERP data for "i voted sticker profile picture", "texas i voted sticker", "vote sticker
  maker" or any Spanish sticker query; their volume in election week is unmeasured.
- Ahrefs shows 0 for every Washington ballot-deadline phrasing tried; GSC after November 3 is the only
  way to know what all-mail state pages earn.
- Etsy returned 403 and Ballotpedia returned an empty page to WebFetch; their content was not read.
- The Texas worked example's mail deadlines, hours minimums and electioneering clause were read on CBS,
  Vote.org and news summaries, not on the official pages, and are marked RE-VERIFY.
- Whether Google will show FAQPage rich results for this site: it will not, under the August 2023 policy,
  unless the policy changes.
