# Guide Visuals Spec

Set November 9, 2026, for the `/voting/` guides. It builds on the Visuals section of `docs/editorial-standards.md`.
Research: a Perplexity thread in the project "VoteSticker.org Research", Search mode, 115 sources
(https://www.perplexity.ai/session/873c88b9-5ffa-4f96-8ba8-bc1742de3309). Its picks rest on the FT Visual Vocabulary,
Urban Institute, Datawrapper, W3C and the UK Government Analysis Function chart guidance. Those sources weren't read directly
this pass, so treat the guidance as Perplexity reports it.

## What "Better Than Anything Else" Means Here

A reader can understand the main comparison, find their state, read the exception, and open the official source. None of that
should need hovering, decoding colors or JavaScript.

| What the best competitors do | What we do instead |
|---|---|
| A colored map with a nine-row table and no sources (MultiState) | A map plus visible state sections. Each has its rule, the law quoted, a source link and a checked date |
| An encyclopedia list (Ballotpedia, NCSL) | Charts built for the question: a threshold dot plot, a timing plot, an eligibility matrix |
| A pie chart image (EAC) | Real text in SVG and HTML: readable by screen readers and search engines, and sharp at any zoom |
| Grey states with no explanation | A coverage line ("checked 10 of 10 states with runoffs") and a "not yet checked" status kept separate from "none" |
| English only | Matching Spanish pages and Spanish share images |

## Chart Forms

| Data | Form | Notes |
|---|---|---|
| Which states have which rule | The existing square tile map, as HTML links | Orientation, not the explanation. Caption: "Each tile is one state or D.C. Tile size doesn't show population." |
| Thresholds (30%, 35%, majority) | Horizontal dot plot, one labeled row per state, on a 0 to 100% axis | Label each dot with the rule in words: "More than 30%", "More than half". The table carries the exact legal wording |
| Runoff timing | Dot plot on "Weeks after the first election" | A dot for a fixed interval, a capped line for a legal window. Calendar formulas such as "fourth Tuesday in May" are text, plotted only as the interval they produce in a typical year, with a footnote |
| This year's dates | Not in the guide | They live on the state pages, so the guide stays evergreen |
| Who can vote in which primary | An HTML matrix with words in the cells: Can vote, Can't vote, Depends | Never a light-to-dark scale, because these are categories, not amounts. "Depends" cells link to a footnote |
| Runoff logic | A short step flow, with the same steps written as an ordered list | Example numbers labeled as made up |
| Several rule types side by side | Small multiples with the same scale and order | |

**Categories aren't a ranking.** Don't use two or three blues of increasing depth for unordered categories. Use different
treatments: a navy fill, a gold fill with a navy outline, cream with navy hatching, and an outline only. A letter on every tile
does the same job without color.

| Map category (runoffs) | Treatment | Letter |
|---|---|---|
| Primary and general runoffs | Navy fill, cream text | G |
| Primary runoffs only | Gold fill, navy outline, navy text | P |
| Only on request or for some offices (NC, SD) | Cream with navy hatching, navy outline | L |
| No statewide runoff | Outline only, muted text | (none) |
| Not yet checked | Dashed outline, with a "not yet checked" marker and text | ? |

The final palette and its contrast get checked with the `dataviz` skill before the build. Red `#C1272D` is never a data color.

## How It's Built

**Choice: inline SVG and HTML generated during the PHP build from the state data.** No chart library ships to the browser.

| Option | Use |
|---|---|
| SVG generated in PHP | Dot plots, timing plots, flows |
| HTML and CSS Grid | The tile map, the state sections, the matrix, the tables |
| D3 or Observable Plot at build time | Only if the scales get complicated. Not needed for these forms |
| Chart.js | No. It draws to a canvas, which screen readers can't read |
| Datawrapper embeds | No. We want the markup, print and offline output to be ours |

**One data file feeds everything:** the chart, the tile map, the state sections, the tables, the counts in the text and the share
image. Fields per state rule (adapted from Perplexity's schema):

```
state, election_context (primary | general | special), office_scope,
runoff (yes | no | on_request | limited), threshold_value, threshold_operator (">" | ">="),
threshold_denominator, threshold_text (quoted from law), timing_type (interval | window | formula),
timing_weeks, timing_text, who_can_vote, exceptions,
effective_from, sources[] {url, title, section}, checked, status (verified | qualified | not_checked | recheck)
```

**Legal status and research status are separate fields.** "No runoff" is a finding. "Not yet checked" is a research gap. They
never share one grey.

## Accessibility

- **Static charts:** a `<figure>`, and an HTML heading and summary above the chart. The SVG gets `role="img"` with `<title>` and
  `<desc>`, a unique ID for each instance, and real `<text>` for labels. The full table is a visible HTML table after the chart, not
  packed into `aria-describedby`.
- **The tile map is navigation, not a chart:** `<nav>` with a list of links (`href="#state-tx"`), a "Skip map" link, and accessible
  names like "Texas: rule and official source". No `role="grid"`. The History page already works this way.
- **Contrast:** 3:1 for chart marks, outlines and focus rings against what's next to them; 4.5:1 for text. Gold works only as a
  fill with a navy outline.
- **Never color alone:** letters on tiles, words in matrix cells, direct labels on dots.
- **Reduced motion:** no chart animation at all.
- **Forced colors (Windows contrast themes):** small fixes with system colors (`Canvas`, `CanvasText`, `LinkText`, `Highlight`).
  Never set `forced-color-adjust: none` on a whole chart.
- **Test before publishing:**
  - keyboard only
  - NVDA with Chrome or Firefox
  - VoiceOver with Safari
  - a Windows contrast theme
  - 200% zoom
  - JavaScript off
  - print preview
  - English and Spanish at 320 pixels

## State Selection

- **The URL fragment is the shareable state:** `/voting/runoff-elections/#state-tx`. Each state has a visible section with that ID,
  and `:target` highlights it. This works with no JavaScript.
- **A native `<select>` and a "Show my state" button** are a small enhancement. They move focus only when the button is pressed.
- **`?state=TX` from a state page** is read by a few lines of script that jump to `#state-tx`. Without script, the full page still
  works. This replaces the earlier `?state=` plan as the main mechanism.
- **Use `<details>` only for side explanations** ("How we sort states"). Never hide a source or a key exception inside one.

## Styling

- **Text:** the site's own font, labels near body size, horizontal, `font-variant-numeric: tabular-nums` for percentages and weeks.
- **Direct labels on marks.** A small key only for the map.
- **Three SVG layers:** grid (aria-hidden), data, annotations. Annotations are short and sit on a cream background so gridlines
  don't run through them. Longer caveats go in the HTML.
- **Hatching:** one `<pattern>` with a prefixed ID per chart, used for one meaning only (limited or on request).
- **Container queries:** the tile map shows from about 42rem of container width. Below that, a grouped list ("Primary and general
  runoffs: Georgia, Louisiana, Mississippi"). The table shows in both.
- **Focus:** a 3px navy or `--focus` outline with an offset, the same on tiles, select and links.
- **Print:** hide the picker and the map, keep the tables and their headers on each page, and avoid breaks inside a row.
- Starter CSS for container queries, forced colors and print is in the Perplexity thread, section 4. Adapt it to the site's tokens
  rather than pasting it.

## Share Images

Each guide gets a separate share image per language, not a screenshot. It shows a short title, a simplified tile map, the scope
("States with runoffs, checked [date]") and the Vote Sticker name. It's generated from the same data, the way the existing
`build/og_cards.mjs` makes the state cards.

## Data Displays to Study

From the Perplexity thread, each for one technique, not as a model to copy:
- **UK Government Analysis Function chart guidance:** use as the QA checklist (SVG, annotations, consistent scales, source links).
- **USAFacts:** keeps the summary, the detail and the original government data connected.
- **Washington Post "2°C: Beyond the Limit":** moves from national context to your own place without losing the method.
- **Pew, "Putting federal spending in context":** explains the denominator in plain words next to the chart.
- **ProPublica Electionland:** a verification discipline focused on voters.

## First Components

1. Tile map rebuilt as a reusable component: categories, letters, hatching, a not-checked status, and a list view.
2. A reusable SVG dot and range plot (thresholds, timing).
3. An HTML eligibility matrix with footnotes.
4. A state section and source table renderer.
5. Fragment-based state selection with the optional picker.
6. Print styles and the share-image template.
