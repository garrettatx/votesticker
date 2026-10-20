# Iowa (IA): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/ia/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop.

## Facts

- Source: site/states.js. Status on October 20: In-person OCT 14–NOV 2; started.
- If you run this after NOV 2, early voting is over: use VOTE / NOV 3 with ELECTION DAY as the main message and keep only I VOTED / EARLY from the early sayings.
- Capital: Des Moines. Star at 47% across and 61% down the outline's bounding box (site/state-shapes.js).
- Outline: accurate, at true proportions, never stretched.
- State palette: background #F5F0E1, ink #2B2D42, accent #C9870F. Core 4 reverses it: #2B2D42 background.
- Ready-made images on the site now: 5. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / EARLY, date line BY NOV 2
2. Core Navy big type: VOTE / EARLY, date line BY NOV 2
3. Core Forest badge (no ring): VOTE / EARLY, date line BY NOV 2
4. Core shape in the state palette, reversed (ink color as the background): VOTE / EARLY, date line BY NOV 2
5. Creative: VOTE / EARLY, date line BY NOV 2
6. Creative: I VOTED, second line IOWA
7. Creative: I VOTED, second line IOWA
8. Creative: I VOTED / EARLY, second line IOWA
9. Creative: MAKE A / PLAN, second line VOTE EARLY
10. Creative: IOWA / VOTES, second line NOV 3

Never use: VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/ia/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named ia-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/ia/CHECK.md, then stop.
