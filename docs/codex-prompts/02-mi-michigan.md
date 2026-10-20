# Michigan (MI): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/mi/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop.

## Facts

- Source: site/states.js. Status on October 20: In-person OCT 24–NOV 1; upcoming; county/local days vary.
- Early voting runs OCT 24–NOV 1. If you run this on or after OCT 24, the date line is BY NOV 1 instead. If you run this after NOV 1, early voting is over: use VOTE / NOV 3 with ELECTION DAY as the main message and keep only I VOTED / EARLY from the early sayings.
- Capital: Lansing. Star at 73% across and 82% down the outline's bounding box (site/state-shapes.js).
- Outline: Show both the Upper and Lower Peninsulas, with the gap between them
- State palette: background #E9F1F7, ink #0B3954, accent #21857A. Core 4 reverses it: #0B3954 background.
- Ready-made images on the site now: 3. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / EARLY, date line OCT 24–NOV 1
2. Core Navy big type: VOTE / EARLY, date line OCT 24–NOV 1
3. Core Forest badge (no ring): VOTE / EARLY, date line OCT 24–NOV 1
4. Core shape in the state palette, reversed (ink color as the background): VOTE / EARLY, date line OCT 24–NOV 1
5. Creative: VOTE / EARLY, date line OCT 24–NOV 1
6. Creative: I VOTED, second line MICHIGAN
7. Creative: I VOTED, second line MICHIGAN
8. Creative: I VOTED / EARLY, second line MICHIGAN
9. Creative: MAKE A / PLAN, second line VOTE EARLY
10. Creative: MICHIGAN / VOTES, second line NOV 3

Never use: VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/mi/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named mi-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/mi/CHECK.md, then stop.
