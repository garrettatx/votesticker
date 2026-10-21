# South Dakota (SD): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/sd/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop. Ask for the second
line at 40% of the headline's letter height in the first prompt of every image; an undersized date line was
the most common redraw on October 21. If you are stopped partway, everything saved is used: write each PNG
as soon as it is checked, and keep going in slot order.

## Facts

- Source: site/states.js. Status on October 20: Unchecked; no verified early dates.
- Main message: VOTE / NOV 3, date line ELECTION DAY.
- Capital: Pierre, in the middle of the state. Star at 49% across and 46% down the outline's bounding box (site/state-shapes.js).
- Outline: accurate, at true proportions, never stretched.
- State palette: background #F3EFE5, ink #2A2D34, accent #4F8271. Core 4 reverses it: #2A2D34 background.
- Ready-made images on the site now: 5. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / NOV 3, date line ELECTION DAY
2. Core Navy big type: VOTE / NOV 3, date line ELECTION DAY
3. Core Forest badge (no ring): VOTE / NOV 3, date line ELECTION DAY
4. Core shape in the state palette, reversed (ink color as the background): VOTE / NOV 3, date line ELECTION DAY
5. Creative: VOTE / NOV 3, date line ELECTION DAY
6. Creative: I VOTED, second line SOUTH DAKOTA
7. Creative: I VOTED, second line SOUTH DAKOTA
8. Creative: I VOTED, second line SOUTH DAKOTA
9. Creative: MAKE A / PLAN, second line VOTE NOV 3
10. Creative: VOTE / NOV 3, date line ELECTION DAY

Never use: VOTE EARLY, I VOTED EARLY, VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/sd/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named sd-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/sd/CHECK.md, then stop.
