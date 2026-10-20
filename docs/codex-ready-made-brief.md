# Ready-Made Sets with Codex: The Brief

Started October 20, 2026 with the Mississippi test. Codex reads this file at the start of every state.
It adds to `docs/round3-prompts-one-state.md` ("RULES FOR ALL 10 IMAGES") and wins where they differ.
Every lesson below came from a real image that failed; add new ones the same day they are found.

## How a State Runs

1. One state per Codex chat, in the Votesticker project. Never two chats on the same state. Run two or
   three chats at a time at most: each one draws on the same plan image limit.
2. Write `incoming/codex/<abbr>/BRIEFS.md` first: each image's words, concept, colors, and where the
   outline and star go. List the palettes and concepts already used so none repeat.
3. Generate one image at a time. Save each final PNG as `<abbr>-NN-<concept>-<palette>.png`. Never
   delete or overwrite a version Garrett or Claude asked to keep; a redo gets a new filename.
4. Write `incoming/codex/<abbr>/CHECK.md`: pixel size, pass or fail on each check below, what was
   redone and why. Then stop.
5. Claude reviews every image, fixes centering, scale and spacing, and builds a contact sheet.
   Garrett approves. Only approved images go live.

Limits: write only inside `incoming/codex/`. No git, no deploy, no edits anywhere else.

## The Standard

Ready-made sets are the designer tier: each image looks composed by hand, with strong type hierarchy
and real contrast. The maker in `site/app.js` (Sticker, Poster, Stamp, Big type, Badge) is the template
tier. Images 5 to 10 must not resemble those layouts or each other.

**What Garrett picks (October 20):** diagonal type, and palettes that don't look like election
material: chartreuse on cobalt, gold on maroon, cream on plum, gold on teal, cream on burnt orange.
In images 5 to 10, lean on these over red, white and blue; the brand red and navy stay in cores 1
and 2. An arched varsity headline, brush lettering over a clean block line (Nebraska), and a map set beside
the type also landed well.

Sets offer variety on purpose: people pick the one they like, so not every image has to be everyone's
favorite. Each one still has to pass the rules.

## Words

- Dates and which messages are true come only from `site/states.js`. Today's date decides "BY <last
  day>" for windows already open. Mail states say "DUE NOV 3".
- Two headline lines at most, 10 characters or fewer each. The second, smaller line (date line or
  state name) is a single line, never below a third of the headline's letter height.
- Message mix per set: images 1 to 4 and 5 to 7 use the state's main message; 8 to 10 say I VOTED with
  the state name as the second line. Optional extras where true: I VOTED / EARLY (verified early
  voting), I VOTED / BY MAIL and MY BALLOT IS IN (all-mail states), MAKE A / PLAN with VOTE NOV 3,
  <STATE> / VOTES with NOV 3.
- States without early voting open to everyone (Mississippi, Alabama, New Hampshire) never get EARLY,
  BY MAIL, or MY BALLOT IS IN.
- A long state name works as a headline only in very heavy condensed type (MISSISSIPPI VOTES passed
  the composition check; confirm at 48 px before reusing the pattern).

## Composition Lessons

- **Gap under the map.** The lowest point of the outline, usually the southern tip, must clear the
  headline by at least the height of the second line's letters. Three of the first five Mississippi
  images nearly touched. Check the tip, not the outline's general edge.
- **No rings, borders or frames,** including the badge core image. They cannot be recentered.
- **Strong backgrounds only.** Saturated or dark. A pale tint (lavender, near white) disappears
  against the white and gray of social feeds and reads as an empty circle.
- **Two light backgrounds per set at most.** Mississippi came back with four (cream, magnolia, cream,
  lavender), and a light disk has no edge on a white feed. Core 1 (Cream) counts as one. Most state
  palettes in `build/prompt_states.py` have a pale background, so core 4 uses the state palette
  reversed: the ink color as the background, the pale color for type, the accent for the bar.
- **Balance the visual weight.** Centering is done by bounding box, so a staggered layout with empty
  space in one corner still looks shifted inside the circle. Stack or balance the type.
- **No accent marks near words.** A small slash or tick beside a word reads as punctuation or a typo
  at 48 px (MAKE A PLAN had a gold slash after "A").
- **No repeated concepts.** Two images on the same diagonal over the same plum background read as a
  color swap. Vary composition and palette; check BRIEFS.md before each image.
- **Place the star from the repo's map data.** Before drawing, read the state's `w`, `h` and `cap` in
  `site/state-shapes.js` and give the star's position as a share of the outline's width and height
  (Jackson: 41% across, 56% down). Most Mississippi stars were drawn too far north.
- **"Flat" means no visible gradient or texture.** Image tools add 1 to 5 RGB levels of noise; that
  passes. Don't fail or redraw for it.
- **Every shape has a job.** The outline, the type, a plate or bar that backs the type (a stepped plate
  that hugs the words is fine, Minnesota VOTE EARLY), a split background, or one small accent smaller
  than the outline. No abstract blocks or stair-steps (Minnesota had a gold step shape
  bigger than the map that read as a bar chart).
- **I stays on the same line as VOTED,** never alone on its own line (it reads as a numeral 1 or a
  pillar). **Type families:** the heavy condensed sans, or brush lettering. No slab or wood-type
  serifs; they look out of family next to the rest of the set (Nebraska).
- **No oversized initial letter.** A giant serif I beside the map reads as a column at 48 px
  (Mississippi 07, Idaho I VOTED).
- **Proven concepts can repeat across states; each set still needs its own ideas.** Strong concepts
  from earlier states may come back (the tilted plum I VOTED from Mississippi 08 and Idaho is a
  favorite). At least three of images 5 to 10 must be concepts no earlier state used: read the
  BRIEFS.md files in incoming/codex/ first, and say in BRIEFS.md which images are new ideas.
- **The accent bar holds the date line, never the headline.** Mississippi's Navy core put the red bar
  behind NOV 3 instead of ELECTION DAY.
- **Red with blue** only in the brand core palettes (Cream, Navy). Never as opposing halves.

## Not Codex's Job

- Star placement, map gaps, centering and fitting. Claude runs `build/codex_finish.py <ABBR> <out> <pngs...>`,
  which finds the outline, moves the star, opens the gap, then runs `build/fit_to_circle.py`, which centers the
  artwork and scales it evenly inside the safe circle. A strong composition that only breaks the circle
  or sits off center is kept, not redrawn.
- Small spacing fixes on flat backgrounds (moving a map up, removing a stray mark). Claude does these.

## Results So Far

- **Mississippi (October 20):** 13 files for 10 slots. Every background was flat, all type was correct,
  and after fitting every image sat inside the circle (scales 0.75 to 1.0). Codex made 1254 px PNGs,
  which the fit tool resizes to 1080. Its High-setting self-check caught the core 2 accent-bar error
  on its own. Second lines (ELECTION DAY, the state name) don't read at 48 px in any design; the
  headline does, which is the rule.

## Checking Images

- Look at the file, not the chat preview. The Codex app fades images where they meet the top of the
  window while scrolling, which looks like a gradient but isn't in the file.
- Check every image at full size and as a 48 px circle.
- Long prompts pasted into the Codex app have arrived cut off. Point Codex at this file instead of
  pasting rules.
