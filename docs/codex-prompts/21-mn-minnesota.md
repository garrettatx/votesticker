# Minnesota (MN): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/mn/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop.

## Facts

- Source: site/states.js. Status on October 20: In-person SEP 18–NOV 2; started.
- If you run this after NOV 2, early voting is over: use VOTE / NOV 3 with ELECTION DAY as the main message and keep only I VOTED / EARLY from the early sayings.
- Capital: Saint Paul, in the east-central part of the state, not Minneapolis or Duluth. Star at 53% across and 75% down the outline's bounding box (site/state-shapes.js).
- Outline: Include the small Northwest Angle notch at the top and the Arrowhead in the northeast
- State palette: background #EAF2F8, ink #102A43, accent #3C8D5A. Core 4 reverses it: #102A43 background.
- Ready-made images on the site now: 5. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / EARLY, date line BY NOV 2
2. Core Navy big type: VOTE / EARLY, date line BY NOV 2
3. Core Forest badge (no ring): VOTE / EARLY, date line BY NOV 2
4. Core shape in the state palette, reversed (ink color as the background): VOTE / EARLY, date line BY NOV 2
5. Creative: VOTE / EARLY, date line BY NOV 2
6. Creative: I VOTED, second line MINNESOTA
7. Creative: I VOTED, second line MINNESOTA
8. Creative: I VOTED / EARLY, second line MINNESOTA
9. Creative: MAKE A / PLAN, second line VOTE EARLY
10. Creative: VOTE / EARLY, date line BY NOV 2

Never use: VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/mn/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named mn-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/mn/CHECK.md, then stop.
