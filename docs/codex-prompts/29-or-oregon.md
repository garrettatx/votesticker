# Oregon (OR): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/or/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop. Ask for the second
line at 40% of the headline's letter height in the first prompt of every image; an undersized date line was
the most common redraw on October 21. If you are stopped partway, everything saved is used: write each PNG
as soon as it is checked, and keep going in slot order.

## Facts

- Source: site/states.js. Status on October 20: Mail ballot window OCT 14–NOV 3; started.
- Main message: VOTE / BY MAIL, date line DUE NOV 3.
- Capital: Salem, in the northwest, not Portland. Star at 19% across and 31% down the outline's bounding box (site/state-shapes.js).
- Outline: accurate, at true proportions, never stretched.
- State palette: background #E6EFE9, ink #1F3B2C, accent #2F6690. Core 4 reverses it: #1F3B2C background.
- Ready-made images on the site now: 5. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / BY MAIL, date line DUE NOV 3
2. Core Navy big type: VOTE / BY MAIL, date line DUE NOV 3
3. Core Forest badge (no ring): VOTE / BY MAIL, date line DUE NOV 3
4. Core shape in the state palette, reversed (ink color as the background): VOTE / BY MAIL, date line DUE NOV 3
5. Creative: VOTE / BY MAIL, date line DUE NOV 3
6. Creative: I VOTED, second line OREGON
7. Creative: I VOTED, second line OREGON
8. Creative: MY BALLOT / IS IN, second line OREGON
9. Creative: MAKE A / PLAN, second line VOTE NOV 3
10. Creative: OREGON / VOTES, second line NOV 3

Never use: VOTE EARLY, I VOTED EARLY.

## Output

- incoming/codex/or/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named or-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/or/CHECK.md, then stop.
