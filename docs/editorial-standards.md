# Editorial Standards for Guides

Set November 9, 2026. These standards govern the guides under `/voting/` and any page that explains how voting works.
`docs/brand-brief.md` still sets the voice. This file adds what a guide needs on top: attribution, dates, sources,
neutrality, structure and visuals. The guide plan is `docs/guides-plan.md`.

## The Reader Promise

**In two minutes, understand how it works and what applies in your state.**

Every guide answers these questions, in this order:

1. What is it? (The answer to the search, in the first paragraph.)
2. How does it work?
3. What applies in my state?
4. Where do I confirm it, and what do I do next?

If a paragraph doesn't help answer one of those, cut it or move it lower.

## Neutral by Design

Vote Sticker is pro-voting and never pro-candidate, pro-party or pro-reform. Guides explain how a system works. They never
argue for adopting, keeping or repealing one.

- **Describe what each kind of voter can do.** "Voters not registered with a party can't vote in a party's primary" beats any
  label for that rule.
- **Keep "who can vote" separate from "how the ballot works."** An open primary doesn't mean every candidate is on one ballot.
- **Ask the same questions of every system:** who can vote, what's on the ballot, what decides the winner, what happens next.
- **No judging words in our voice:** fairer, better, common-sense, rigged, undemocratic, shuts out, extreme, broken.
- **If a debate needs explaining,** give it its own short section and attribute each argument to the people who make it: "Supporters
  of open primaries say...", "Some parties argue...". Never decide it. Never give a weak claim equal space to look balanced.
- **Groups that advocate for a system,** including ones that call themselves nonpartisan, are cited only for what they argue,
  and named as advocates.
- **Never color party-related data red against blue.** See Visuals.
- **Examples use made-up numbers,** clearly labeled, not real races or candidates.

## Attribution and Dates

**Byline:** "By Vote Sticker", linked to the About page's section on how we check facts. No personal names. A "Reviewed by"
line appears only if a second person actually reviews a guide, and it says what they checked.

| Date | Means | Shown | Schema |
|---|---|---|---|
| Published | The day this language edition went live | Under the byline | `datePublished` |
| Updated | A real change to the explanation, a rule, or the state data. Not a typo, link fix, template, menu or footer change | Only when later than Published, plus a one-line note at the bottom | `dateModified` |
| Facts reviewed | Someone completed the review checklist below, even if nothing changed | Under the byline | None |
| Checked | One state fact read on its official source | Beside the fact, in the state table | None |

- Dates come from the guide's data file, never from the build date. Visible and structured dates must match.
- English and Spanish keep separate dates. The Spanish edition is marked reviewed only after a Spanish check.
- The sitemap `lastmod` follows Updated.
- Use date-only values, with no timezone offset.
- Keep other dates out of a guide's prose where possible; Google advises it so it picks the right page date. Per-state dates
  live in the state table.
- "Verified" is reserved for an act we can name. Reading a source is "checked".

Byline template:

> By Vote Sticker · Published March 3, 2027 · Facts reviewed March 3, 2027 · How we check facts

## Sources

The rule: rank by the claim, not the institution.

| Rank | Source | Use it for | Never use it for |
|---|---|---|---|
| 1 | State or local election office pages; statutes and regulations | Anything a voter acts on: who can vote, thresholds, deadlines | |
| 2 | U.S. Election Assistance Commission | National terms and processes: canvass, certification, provisional ballots | Proof of one state's rule |
| 3 | Congressional Research Service | Federal and legal background | Current state rules; check the report's date |
| 4 | NCSL, the EAC state spreadsheet | Comparing states; finding the statute | The source of record for a state row |
| 5 | Academic research | Effects such as turnout, with place, period and limits stated | Current law |
| 6 | Ballotpedia, Wikipedia, news | Finding leads | Support for any claim on its own |
| 7 | Advocacy groups | What that group argues, attributed | Definitions, current law, or effects |

- **Link where the claim is.** The link goes right after the sentence it supports, or in the table row. Link text names the
  document ("Texas Election Code 172.003"), never "source" or "here". Link to the specific page, never a homepage.
- **Every state row has its own source and checked date.**
- **Statutes get the section number.** Pair a statute with the election office's plain-language page when one exists.
- **Read it on the page.** A summary, a search snippet or an AI answer is a lead, never the source.
- **Trust the state rows, not a summary.** Check the per-state rows against the source's own totals.
- **When sources conflict,** don't pick one quietly. Find out why (effective date, court order, election type), and leave the
  claim out until it's settled.
- **Counts come from the data.** "In 20 states" is computed from the state rows, never typed.
- Research notes go in `docs/research/guide-<slug>.md`: the prompts, the sources read, and what wasn't found.

## Writing

The brand brief's voice holds: a friendly neighbor who checked the facts. For guides:

- **Answer first.** The first paragraph defines the topic in 40 to 60 words, the way someone searching it would ask.
- **Readers aren't experts.** Aim for 8th to 10th grade reading level. Explain a term once, the first time it appears.
- **Short sentences.** Split anything over about 25 words. One claim, one detail, one reason it matters.
- **Claim before number.** Say it plainly, then give the number. One headline number, one or two supporting.
- **No triples in prose.** Lead with the most specific example.
- **No empty encouragement:** "your voice matters", "now more than ever", "make a difference", "civic duty".
- **Admit limits in the sentence,** calmly: "rules for presidential primaries differ in some states." Every guide ends with what
  it doesn't cover.
- **Don't restate,** don't explain what the reader already knows, and don't narrate the page ("In this guide we'll...").
- **Bridges between sections:** one sentence linking what the reader just learned to what comes next.
- **Links:** link a specialized term the reader may not know (canvass, top-two primary), never a household one (voter
  registration, Election Day). Vary anchor text and name the destination.
- Title Case headings, no em dashes, and the humanizer pass under the brand voice.

## Titles and URLs

- **URL is the topic name only,** under `/voting/`: `/voting/runoff-elections/`, not `/voting/what-is-a-runoff-election/`. No
  year, no "guide", no "how-to". Spanish uses the same slug under `/es/`.
- **One rule:** a page about one year has the year in its URL; a page about every year doesn't.
- **Check Ahrefs before naming a guide.** Use the term people search. Two distinct parent topics get two pages; one parent topic
  gets one page, never two near-duplicates.
- **Title tag:** front-load the search term, keep it under about 60 characters, end with "| Vote Sticker", and match the H1 closely.
- **Headings read the way people ask:** "Who Can Vote in a Closed Primary", "What Happens if No One Gets 50%".

## Structure of a Guide

1. **H1 and the answer** (40 to 60 words).
2. **Byline and dates.**
3. **Key facts:** three to five bullets, counts derived from data.
4. **How it works:** the mechanics, with a step flow or comparison table.
5. **In your state:** a state picker, the state's rule with its source and checked date, and a link to that state's page section.
   Map and full table below.
6. **Questions people ask:** from real queries.
7. **What this guide doesn't cover,** and where to check: your state or local election office.
8. **Make your sticker:** one line linking the state's sticker, never a hard sell.
9. **Change notes,** if any.

No separate bibliography; sources are inline. State pages link to the guide, and the guide links to the state page's section.

## Visuals

- **The takeaway sentence goes above the chart.** The caption carries the source and checked date only.
- **Built in HTML and inline SVG,** with no chart library. Reuse the site's tile map.
- **The table stays visible** under every map: all 51 rows, each with its source and checked date. It's what a reader uses when the
  map fails, and it's what search engines and AI tools read.
- **Never color alone:** each tile shows the state abbreviation and a category letter or pattern, with a legend.
- **No hover-only information.** Tiles are keyboard focusable.
- **Map only when all 51 rows are checked.** Until then, show a table of the checked states. A map with grey gaps looks complete
  and isn't.
- **Contrast:** 3:1 for chart shapes against their background, 4.5:1 for text.
- **Colors** from the brand: ink navy `#14295E`, a mid blue `#3F64B0`, gold `#F2C14E` with a navy outline, cream with navy
  hatching, and grey `#8C8F96` for no data. **Red `#C1272D` is never a data color**, because red against blue reads as party.
- **Illustrative diagrams say so** ("Example with made-up numbers").
- **Check every visual at 320, 375, 768 and 1280 pixels,** in English and Spanish.

## Review

| Content | Review | Also when |
|---|---|---|
| Definitions and mechanics | Once a year | A correction comes in |
| State rules | Every election cycle, before the first primary deadline | A law, court order or official guidance changes |
| Dated instructions | Before the earliest deadline, and a final check before the election | Official notices |
| Spanish | With every factual change | |

Each guide has a review-due date, and the build fails when it has passed, as `qa_stale` does for state facts.

**Review checklist:** scope (which elections and offices); who can vote; the calendar; procedures; source versions and
effective dates; whether archived material is being read as current; and English and Spanish parity in text, tables, dates,
schema and links.
