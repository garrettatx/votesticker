# Hawaii (HI): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/hi/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop.

## Facts

- Source: site/states.js. Status on October 20: Mail ballot window OCT 16–NOV 3; started.
- Main message: VOTE / BY MAIL, date line DUE NOV 3.
- Capital: Honolulu, on Oahu. Star at 44% across and 28% down the outline's bounding box (site/state-shapes.js).
- Outline: Show the main islands in their real arrangement, small; never a single blob
- State palette: background #E5F4F1, ink #0D3B4C, accent #D9583A. Core 4 reverses it: #0D3B4C background.
- Ready-made images on the site now: 5. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / BY MAIL, date line DUE NOV 3
2. Core Navy big type: VOTE / BY MAIL, date line DUE NOV 3
3. Core Forest badge (no ring): VOTE / BY MAIL, date line DUE NOV 3
4. Core shape in the state palette, reversed (ink color as the background): VOTE / BY MAIL, date line DUE NOV 3
5. Creative: VOTE / BY MAIL, date line DUE NOV 3
6. Creative: I VOTED, second line HAWAII
7. Creative: I VOTED, second line HAWAII
8. Creative: MY BALLOT / IS IN, second line HAWAII
9. Creative: MAKE A / PLAN, second line VOTE NOV 3
10. Creative: HAWAII / VOTES, second line NOV 3

Never use: VOTE EARLY, I VOTED EARLY.

## Output

- incoming/codex/hi/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named hi-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/hi/CHECK.md, then stop.
