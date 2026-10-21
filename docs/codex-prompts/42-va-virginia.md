# Virginia (VA): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/va/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop. Ask for the second
line at 40% of the headline's letter height in the first prompt of every image; an undersized date line was
the most common redraw on October 21. If you are stopped partway, everything saved is used: write each PNG
as soon as it is checked, and keep going in slot order.

## Facts

- Source: site/states.js. Status on October 20: In-person SEP 18–OCT 31; started.
- If you run this after OCT 31, early voting is over: use VOTE / NOV 3 with ELECTION DAY as the main message and keep only I VOTED / EARLY from the early sayings.
- Capital: Richmond. Star at 74% across and 66% down the outline's bounding box (site/state-shapes.js).
- Outline: accurate, at true proportions, never stretched.
- State palette: background #EDE6DA, ink #233142, accent #8C2F39. Core 4 reverses it: #233142 background.
- Ready-made images on the site now: 6. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / EARLY, date line BY OCT 31
2. Core Navy big type: VOTE / EARLY, date line BY OCT 31
3. Core Forest badge (no ring): VOTE / EARLY, date line BY OCT 31
4. Core shape in the state palette, reversed (ink color as the background): VOTE / EARLY, date line BY OCT 31
5. Creative: VOTE / EARLY, date line BY OCT 31
6. Creative: I VOTED, second line VIRGINIA
7. Creative: I VOTED, second line VIRGINIA
8. Creative: I VOTED / EARLY, second line VIRGINIA
9. Creative: MAKE A / PLAN, second line VOTE EARLY
10. Creative: VIRGINIA / VOTES, second line NOV 3

Never use: VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/va/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named va-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/va/CHECK.md, then stop.
