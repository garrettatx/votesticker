# Alaska (AK): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/ak/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop. Ask for the second
line at 40% of the headline's letter height in the first prompt of every image; an undersized date line was
the most common redraw on October 21. If you are stopped partway, everything saved is used: write each PNG
as soon as it is checked, and keep going in slot order.

## Facts

- Source: site/states.js. Status on October 20: In-person OCT 19–NOV 2; started.
- If you run this after NOV 2, early voting is over: use VOTE / NOV 3 with ELECTION DAY as the main message and keep only I VOTED / EARLY from the early sayings.
- Capital: Juneau, in the southeast panhandle. Star at 88% across and 77% down the outline's bounding box (site/state-shapes.js).
- Outline: Include the Aleutian chain and the southeast panhandle; keep it at true proportions, never squashed
- State palette: background #E6F0F6, ink #0E2A47, accent #F2B33D. Core 4 reverses it: #0E2A47 background.
- Ready-made images on the site now: 5. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / EARLY, date line BY NOV 2
2. Core Navy big type: VOTE / EARLY, date line BY NOV 2
3. Core Forest badge (no ring): VOTE / EARLY, date line BY NOV 2
4. Core shape in the state palette, reversed (ink color as the background): VOTE / EARLY, date line BY NOV 2
5. Creative: VOTE / EARLY, date line BY NOV 2
6. Creative: I VOTED, second line ALASKA
7. Creative: I VOTED, second line ALASKA
8. Creative: I VOTED / EARLY, second line ALASKA
9. Creative: MAKE A / PLAN, second line VOTE EARLY
10. Creative: ALASKA / VOTES, second line NOV 3

Never use: VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/ak/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named ak-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/ak/CHECK.md, then stop.
