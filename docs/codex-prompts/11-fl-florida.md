# Florida (FL): Ready-Made Set for Codex

Read docs/codex-ready-made-brief.md first and follow it. Then make this state's set. Write only inside
incoming/codex/fl/. No git commands, no deploy. Stop when CHECK.md is written.

Save usage: redraw only for wrong words, a broken or unrecognizable map, or a rule break. Claude fixes
star position, map gaps, maps within 8% of true width, centering and the circle crop. Ask for the second
line at 40% of the headline's letter height in the first prompt of every image; an undersized date line was
the most common redraw on October 21. If you are stopped partway, everything saved is used: write each PNG
as soon as it is checked, and keep going in slot order.

## Facts

- Source: site/states.js. Status on October 20: In-person OCT 24–31; upcoming; county/local days vary.
- Early voting runs OCT 24–31. If you run this on or after OCT 24, the date line is BY OCT 31 instead. If you run this after OCT 31, early voting is over: use VOTE / NOV 3 with ELECTION DAY as the main message and keep only I VOTED / EARLY from the early sayings.
- Capital: Tallahassee. Star at 44% across and 9% down the outline's bounding box (site/state-shapes.js).
- Outline: Include the panhandle at its true length; keep the outline small rather than squashing it
- State palette: background #E8F6F3, ink #0B4F6C, accent #E07A2B. Core 4 reverses it: #0B4F6C background.
- Ready-made images on the site now: 5. This set adds ten; Claude decides what stays.

## The Ten Images (slash = line break, not printed)

1. Core Cream shape: VOTE / EARLY, date line OCT 24–31
2. Core Navy big type: VOTE / EARLY, date line OCT 24–31
3. Core Forest badge (no ring): VOTE / EARLY, date line OCT 24–31
4. Core shape in the state palette, reversed (ink color as the background): VOTE / EARLY, date line OCT 24–31
5. Creative: VOTE / EARLY, date line OCT 24–31
6. Creative: I VOTED, second line FLORIDA
7. Creative: I VOTED, second line FLORIDA
8. Creative: I VOTED / EARLY, second line FLORIDA
9. Creative: MAKE A / PLAN, second line VOTE EARLY
10. Creative: FLORIDA / VOTES, second line NOV 3

Never use: VOTE BY MAIL, I VOTED BY MAIL, MY BALLOT IS IN.

## Output

- incoming/codex/fl/BRIEFS.md before drawing (words, concept, colors, map and star position).
- Ten PNGs named fl-01-<concept>-<palette>.png onward, redos under new names.
- incoming/codex/fl/CHECK.md, then stop.
