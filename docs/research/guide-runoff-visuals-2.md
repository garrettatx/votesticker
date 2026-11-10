# Runoff Guide: Visuals and Content Ideas (Second Pass)

Nov 10, 2026. One Perplexity prompt in the project "VoteSticker.org Research", Search mode, 146 sources:
https://www.perplexity.ai/session/457b92ba-98c2-428f-a10e-6e264e02552e . Everything here is ideas, not facts. Legal claims it
made are only usable after they are read on the official page (see `docs/research/guide-runoff-elections.md`). Builds on
`docs/guide-visuals-spec.md`; conflicts with it are listed at the end.

Brand tokens used below: `--cream #FBF8F0`, `--navy #14295E`, `--gold #F2C14E`, `--muted #5A5F6B`. Red `#C1272D` never
appears in a chart. Contrast checked: navy on cream 13.1:1, muted on cream 6.0:1, navy on gold 8.3:1, gold on cream 1.6:1
(so gold is never a mark or a line on cream, only a fill inside a navy outline).

## Techniques Worth Adopting

### 1. One Record per State, Election Type and Office Scope

Perplexity's strongest structural point: South Dakota alone has different rules for its covered primaries and for
municipalities, so one row per state misleads. Add these fields to the spec's schema:

- `threshold_operator` already exists; make sure SD is `>=` (35% or more) and NC and majority states are `>`.
- `runoff_trigger`: `automatic` | `on_request` (NC). Show it beside the threshold, not in a footnote.
- `timing_days` (exact), with `timing_weeks` derived. Never round silently.
- `anchor_event`: `first_election` | `canvass` | `certification`. Only compare like anchors on the timing plot.
- `min_candidates` (SD: 3; AL and AR: more than two or three).

### 2. Threshold Dot Plot: Plot the Boundary, Say the Operator in Words

- One row per state, so equal values never collide. No jitter.
- Plot the dot exactly at 30, 35 or 50. Do not nudge to 30.1 to imply "more than."
- Direct label next to each dot: "More than 30%", "At least 35%", "More than half". The table carries the legal wording.
- A dashed vertical guide at 50% labeled "Half" helps readers see that "more than half" sits just past it.
- PHP scale: `x = plotLeft + pct / 100 * plotWidth`. Escape all labels with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

```css
.vs-chart { color: var(--navy); background: var(--cream); }
.vs-chart text { fill: currentColor; font: 1rem/1.3 var(--font-body); font-variant-numeric: tabular-nums; }
.vs-chart .dot { fill: var(--navy); stroke: var(--navy); }
.vs-chart .row-guide { stroke: var(--muted); stroke-dasharray: 2 5; vector-effect: non-scaling-stroke; }
.vs-chart .axis { stroke: var(--muted); vector-effect: non-scaling-stroke; }
```

### 3. Timing Plot: Dots for Fixed Intervals, Capped Lines for Windows

- Axis "Weeks after the first election", 2 to 12. Dot = one fixed interval (SC 2, AL/AR/GA/MS 4, SD 8, NC 10).
- Capped line = a legal window: `<line>` plus a `<path d="M x1 y-7 v14 M x2 y-7 v14">` for the caps.
- Calendar formulas (Texas "fourth Tuesday in May", Oklahoma "fourth Tuesday of August") are text, plotted as the typical
  interval with a footnote marker, as the spec already says.
- If a record falls outside 2 to 12 weeks, widen the axis or explain it in HTML. Never clamp it to the edge.

### 4. Two Generated Layouts Instead of One Shrinking SVG (Adapted)

`viewBox` scales text with the drawing: an 18-unit label in an 800-wide viewBox renders at about 7px on a 320px phone.
Perplexity's fix is to hide the chart below a container width and show only the table. Better fit for the spec: generate two
SVGs from the same data in PHP and switch with a container query.

- Wide (from about 48rem of container; wider for Spanish): state label column on the left, plot on the right.
- Narrow (`viewBox="0 0 320 ..."`, rendered near 1:1): the state name sits on its own line above each row's track, so the
  track gets the full width and labels stay body size.

```css
.vs-figure { container: vsfig / inline-size; }
.vs-chart--wide { display: none; }
@container vsfig (min-width: 48rem) {
  .vs-chart--wide { display: block; }
  .vs-chart--narrow { display: none; }
}
:lang(es) .vs-figure { container-name: vsfig-es; }
@container vsfig-es (min-width: 54rem) { /* same swap, later breakpoint */ }
```

Both SVGs get `width:100%; height:auto`. Only one is visible at a time, and `display:none` hides the other from screen readers
too, so the figure is announced once.

### 5. Accessible Figure Markup

```html
<figure class="vs-figure" aria-labelledby="thr-cap">
  <svg class="vs-chart vs-chart--wide" viewBox="0 0 800 420" role="img"
       aria-labelledby="thr-t thr-d">
    <title id="thr-t">Vote share needed to avoid a runoff, by state</title>
    <desc id="thr-d">Most states need more than half. North Carolina needs more than 30%. South Dakota needs at least 35%.
      Full values and sources are in the table below.</desc>
    ...
  </svg>
  <figcaption id="thr-cap">Source: state laws, each linked in the table. Checked [date].</figcaption>
</figure>
<table>...</table>
```

- Unique IDs per instance (prefix with the chart slug and language), including `<pattern>` and marker IDs.
- Keep `<desc>` to one or two sentences; the visible table is the long description (matches the spec).
- No `tabindex` on static dots, no tooltips.

### 6. Who Can Vote Matrix: Words in Cells, a Note for Every "Depends"

- Native `<table>` with `<caption>`, `scope="col"` and `scope="row"`. Cells: "Can vote", "Can't vote", "Depends", and a
  separate "Not checked yet" for research gaps (spec already separates these).
- Every "Depends" cell carries a short visible note (`<span class="cell-note">`) and a footnote link. Example for Texas
  skippers: "Yes, unless you joined another party this year."
- Treatments, not shades: "Can vote" navy text with a navy left rule; "Can't vote" muted text, no fill; "Depends" cream with
  navy hatching behind a solid cream label box; "Not checked yet" dashed outline.
- Phones: the desktop matrix is states by three scenarios. At 320px, keep table semantics and switch to one small two-column
  table per state (scenario, answer) inside each state section, rather than `display:block` on table cells.
- Spanish labels: "Puede votar", "No puede votar", "Depende", "Sin verificar".

### 7. Step Flow, Scoped to a Majority Primary

- Five boxes: first election, count, "More than half?", yes branch "Winner decided", no branch "Top two go to a runoff".
- Same steps as an `<ol>` right after (the spec already requires this). The SVG `<desc>` summarizes in one sentence.
- North Carolina gets its own short note with the request step, not a footnote on the generic flow.
- Boxes: navy 1.5px outline, cream fill; the "Runoff" box gold fill with navy outline as the one emphasis.

### 8. Hatching That Survives Print and Forced Colors

```html
<pattern id="thr-en-hatch" patternUnits="userSpaceOnUse" width="8" height="8">
  <path class="hatch" d="M-2 2l4-4M0 8l8-8M6 10l4-4"/>
</pattern>
```

```css
.hatch { stroke: var(--navy); stroke-width: 1.25; fill: none; }
.hatched { fill: url(#thr-en-hatch); stroke: var(--navy); stroke-width: 1.5; }
```

The meaning comes from strokes, not a background fill, so it prints without "background graphics." Always pair with an
outline and a word or letter.

### 9. Forced Colors and Print

```css
@media (forced-colors: active) {
  .vs-chart { color: CanvasText; background: Canvas; }
  .vs-chart text { fill: CanvasText; }
  .vs-chart .dot, .vs-chart .hatch { fill: CanvasText; stroke: CanvasText; }
  .vs-chart .axis, .vs-chart .row-guide, .vs-chart .flow-box { stroke: CanvasText; }
  .vs-figure a { color: LinkText; }
}
/* Only if testing shows a pattern collapsing to a flat fill: */
@media (forced-colors: active) {
  .hatched, .hatch { forced-color-adjust: none; color: CanvasText; stroke: CanvasText; }
}

@media print {
  .vs-figure, .vs-chart { background: #fff; color: #000; }
  thead { display: table-header-group; }
  tr { break-inside: avoid; }
  .source-url { overflow-wrap: anywhere; }
}
```

Do not rely on `print-color-adjust: exact`; it is only a hint.

## Content Ideas

Perplexity's sources for these are official pages, but each claim still needs the check in the research file before use.

1. **"Most votes" versus "enough votes."** The plain-language core of "what is a runoff": finishing first isn't always
   winning. A boundary example with made-up numbers: 500 of 1,000 votes is exactly half and isn't a majority; 501 is. Perplexity
   cites the S.C. Election Commission's explanation of a majority (not read; the statute 7-17-610 already says to divide the
   total by two).
2. **"I skipped the first round. Can I vote?"** Put this above the state table, not in the FAQ. It is the question competitors
   handle least. Now answerable for eight states from official pages (see Fourth Check).
3. **Three-scenario answer for party rules:** voted in this party's primary, voted in the other party's, skipped. Texas
   adds a fourth: someone who skipped but signed an affiliation oath or joined a party convention (Elec. Code 162.003, 162.013,
   already in the research file).
4. **"Does a runoff happen automatically?"** Not in North Carolina: the runner-up has to ask (G.S. 163-111(b), already read).
   Show "automatic" or "on request" beside every threshold.
5. **"Count again" versus "vote again."** Recount compared with runoff, in two short columns. Perplexity cites G.S. 163-111(d),
   where a tie in some county races triggers a recount and then a second primary if the tie holds. Ranked choice as a third
   column: one ballot, counted in rounds, no second trip.
6. **Ties can put three names on the ballot** (SDCL 12-6-51.1, already read). A one-line exception, not part of the definition.
7. **"Can I register between the two elections?"** Separate from "did you vote the first time." Now answerable for most
   states (Fourth Check): no in AR, MS, NC and SC; yes in OK and SD; Texas depends on the 30-day effective date.
8. **Opening answer for "when does a runoff election occur":** "Two to ten weeks later in most states, on a date set by law."
   Then the timing plot. (Texas's 12 weeks is the outlier; keep it in the chart with its footnote.)

Spanish term: Perplexity suggests "elección de segunda vuelta" as the main term, keeping official names like "second primary"
where a state uses them. Check against the site's Spanish glossary before adopting.

## Published Explainers to Study

From the thread; each for one technique. Links not opened in this pass.

- **NYC Board of Elections, Ranked Choice Voting** (vote.nyc): one action, one short sentence, with correct and incorrect ballot
  examples. Use the pattern for "count again" versus "vote again."
- **NYC Votes, "How Votes Are Counted"** (nycvotes.org): separates "how it's counted" from "your ballot." Model for pairing the
  step flow with "What this means for you."
- **Virginia Department of Elections, Ranked Choice Voting** (elections.virginia.gov): keeps what the voter does separate from
  what officials do.
- **Center for Civic Design, "Displaying Ranked Choice Voting Election Results"** (civicdesign.org): overview, then steps, then
  what it means for one ballot.
- **Center for Civic Design ballot design guidance** (hosted by Connecticut, ct.gov): make the correct explanation visually
  dominant; never give a misconception equal weight. Useful for the misconceptions section.

## Conflicts and Decisions for `docs/guide-visuals-spec.md`

1. **Narrow screens.** Perplexity: hide the chart below a container width and show only the table. The spec doesn't say what
   charts do at 320px and requires every visual to be checked there. Recommendation above: a second, narrow SVG layout rather
   than hiding it. Needs a decision.
2. **Threshold labels.** The spec's examples are "More than 30%" and "More than half." South Dakota needs "At least 35%"
   (12-6-51.1: "receives thirty-five percent"). The draft's key facts line ("more than 30% and 35%") should change too.
3. **Print.** Perplexity prints only the tables and hides charts. The spec hides only the picker and map. Keeping the dot plots
   in print is fine if the narrow layout is used for print width; decide which.
4. **`forced-color-adjust: none`.** The spec bans it on a whole chart. Perplexity's scoped use on the hatch pattern only is
   compatible, but only add it if a Windows contrast theme test shows the pattern collapsing.
5. **Matrix on phones.** The spec describes one HTML matrix. Perplexity suggests per-state two-column tables on mobile. Both
   can come from the same data; the per-state table can live in each state section, which the spec already has.
6. **Schema.** `runoff_trigger`, `timing_days`, `anchor_event` and `min_candidates` are additions, not conflicts.
