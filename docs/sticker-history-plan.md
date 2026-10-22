# History of the I Voted Sticker: Page and Callout Plan

Drafted October 22, 2026, before the research arrives. Facts, dates and names wait for that research
and its sources; nothing below states a historical fact yet. Data visualization waits for the data.

## What the Page Is For

Ahrefs shows almost no measured search for the history itself ("history of i voted sticker",
"who invented the i voted sticker", "when did i voted stickers start": 0 each in the US). The page
earns its place another way:

1. **Context for the 25,000-a-month head term.** "i voted sticker" is the site's main keyword, and
   we don't rank for it yet. A sourced history page is the topical depth that a page of sticker
   images doesn't have, and it gives the home page and state pages something to link to with
   "I voted sticker" in the anchor.
2. **The page journalists and AI answers cite.** Every election, reporters write the "where did the
   sticker come from" piece. Low search volume, high citation value. A page with dated, sourced
   claims (our facts-file standard) is what gets quoted.
3. **A reason to stay on the site in the moment it's on someone's mind:** right after they get or
   make a sticker.

## The Page

- **URL:** `/i-voted-sticker-history/` and `/es/i-voted-sticker-history/`. Evergreen, no year, the
  same slug pattern as `/about/` and `/es/about/`.
- **Title and H1:** carry "I Voted Sticker" and "History" (search wins in the title and H1, per the
  brand brief). Working H1: "Where the I Voted Sticker Came From".
- **Facts:** every dated claim gets a source and a check date, like the state guides. One source
  line per section, one "Last checked" per page. A claim we can't source stays off, however often
  it's repeated online (sticker origin stories are full of unsourced lore, so this matters more
  here than anywhere else on the site).
- **Sections, to firm up once the research lands:**
  - The short answer (2 to 3 sentences, written to be quoted)
  - How it started
  - How it spread
  - Why it works (the research on visibility and friends, already summarized in `post-text-rules.md`)
  - State stickers today (see the asset below)
  - Digital stickers and profile pictures (where Vote Sticker fits, one paragraph, no sales pitch)
- **Ends with:** "Make your sticker" to the maker. One CTA, at the end.
- **Schema:** `Article` with author Garrett Digital, `datePublished` and `dateModified`.

### An Asset We Already Have

29 states' facts files carry a verified, sourced "sticker tradition": Minnesota's sticker is set by
law, Georgia's 2026 sticker was painted by Steve Penley, North Dakota's first contest winner was "You
Betcha I Voted", Hawaii tied its sticker to ballot tracking, and so on. No other page collects these.
That's the "State Stickers Today" section, and later the most likely data visualization (a map or
timeline of state contests and designs). Each entry links to its state page, so the history page
also passes links to 29 state pages.

## Where the Callout Goes

A callout here is one or two sentences and a link, written as part of the page, not a boxed promo.
The test for each spot: would someone reading that exact section want to know where the sticker came
from right now? Five spots pass. One link per page, at most.

| # | Where | Why a reader wants it there | Shape |
|---|---|---|---|
| 1 | **State pages, end of the sticker tradition section** (29 states) | They just read their state's sticker story. The national story is the obvious next question. | One line after the state's tradition, before its source line: "[State]'s sticker is part of a longer story. *How the I voted sticker started.*" |
| 2 | **State pages without a tradition** (22 states) | Nothing yet; the slot is empty. | Skip. Adding a section just to hold a link is the kind of filler we cut. Revisit if a state's research finds a real tradition. |
| 3 | **Home page FAQ**, new question: "Where did the I voted sticker come from?" | The FAQ is where curious questions go, and it feeds FAQPage schema. | A 2 to 3 sentence sourced answer, then the link. Matches the visible FAQ and the JSON-LD (QA already checks that). |
| 4 | **Home page "Why This Exists"** | That section already says friends seeing your sticker matters. The history page carries the research behind it. | Link the existing phrase, no new sentence. |
| 5 | **About page**, where it explains why the site exists | E-E-A-T: shows the site knows its subject. | One linked phrase. |
| 6 | **The 404 page's "Try one of these" list** | Recovers lost visitors with something worth reading. | One list item. |

**Where it does not go:**

- **The hero, the maker, the gallery, the download and "Switch to I voted" moments.** The sticker
  comes first; a history link there pulls people out of the one thing they came to do.
- **Post text.** The suggested posts ask a friend to vote; a history link would dilute that.
- **Every state page by default.** Only where the state has a tradition to connect it to.
- **Resist Now.** Not a topic for their Learn guides, and the history page has no reason to link out
  there.

## Spanish

The page and every callout get Spanish versions through the es-mx skill and `docs/spanish-style.md`.
The FAQ answer follows the same rule as the state FAQs: the visible answer and the schema match.

## Build Order

1. Research arrives: verify each claim against a primary or dated news source, record it in a facts
   file (`build/state-facts/` style, probably `build/history-facts.json`), and drop anything we
   can't source.
2. Write the page in English, humanizer pass, then Spanish.
3. Add the callouts (spots 1, 3, 4, 5, 6) in `site/index.php`, through one helper like `vote_rn()`.
4. QA: `qa_seo.py`, `qa_es.py`, `qa_facts.py` (extend it to check the history page's source lines).
5. Deploy, IndexNow, add the page to the Ahrefs rank tracker with the history phrasings.
6. Data visualization, once we know what the data supports.

## Open Questions for Garrett

- Is a facts file for the history page worth it, or is a source list at the bottom enough? A facts
  file keeps season-to-season updates clean; a list is faster.
- Does the history page get a hero sticker of its own (a vintage-style design from the maker)?
