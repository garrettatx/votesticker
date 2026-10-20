# Nebraska (NE): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/ne/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop.

## Facts

- Source: site/states.js. Status on October 20: In-person; BY NOV 2; no start supplied; county/local days vary.
- Main message: VOTE / EARLY, date line BY NOV 2.
- Capital: Lincoln, not Omaha. Star at 84% across and 73% down the outline's bounding box (site/state-shapes.js).
- Outline: accurate, at true proportions, never stretched.
- State palette: background #F4EFE3, ink #2C2A29, accent #C0392B. Core 4 reverses it: #2C2A29 background.
- Ready-made images on the site now: 3. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / EARLY, date line BY NOV 2
2. Core Navy big type: VOTE / EARLY, date line BY NOV 2
3. Core Forest badge (no ring): VOTE / EARLY, date line BY NOV 2
4. Core shape in the state palette, reversed (ink color as the background): VOTE / EARLY, date line BY NOV 2
5. Creative: VOTE / EARLY, date line BY NOV 2
6. Creative: I VOTED, second line NEBRASKA
7. Creative: I VOTED, second line NEBRASKA
8. Creative: I VOTED / EARLY, second line NEBRASKA
9. Creative: MAKE A / PLAN, second line VOTE EARLY
10. Creative: NEBRASKA / VOTES, second line NOV 3

Never use: VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/ne/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named ne-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/ne/CHECK.md, then stop.
