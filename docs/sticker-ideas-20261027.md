# Sticker Ideas, October 27, 2026

Garrett asked for 12 new state designs and 12 general designs (no state shape), classic and futuristic,
to pick about six of each. All 24 are layouts in `site/app.js` under `IDEAS` (listed in `REVIEW`, not in
the maker's tile row), drawn by the same renderer as the maker and the bespoke sets, so a chosen design
drops into `scripts/render_bespoke.mjs` or the classic set with one line.

- Render: `php -S 127.0.0.1:8791 -t site`, then `node scripts/sheet_ideas.mjs incoming/ideas-20261027`.
- Review page (LAN, while the dist server runs): http://192.168.1.252:8793/ideas-20261027/
- Sheets: every design across eight states, messages and palettes, at 240, 60 and 40 px; the four new
  color combinations across all 24; one 1080 px render per design in `big/`.

## Where the Ideas Came From

Searches (county contest winners for 2025, sticker design trend pieces for 2026, WPA poster lettering):
county winners lean on state symbols, hands, checkmarks and ballot boxes; the trend pieces repeat chrome
and bubble type, pixels, grain, holographic finishes and a 70s warm-retro revival; the WPA material argues
for hand-drawn condensed capitals and seal-like framing. From there: Postmark, Marquee, Ribbon, Arcs,
Stencil, Ballot box, Wave and Calendar are the classic branch; Neon, Horizon, Pixel state, Scoreboard,
Orbit, Blueprint, Chrome, Dusk, Pixel check and Broadcast the futuristic one; Speech bubble, Megaphone,
Done, Echo and Confetti are the brand's own idea (a signal to friends) in modern dress. Nothing was
copied from a county sticker; the state outline, star and type are ours.

Renderer additions, usable by any later design: linear gradients on any item, outlined state shapes,
polylines, a neon glow, a pixel sampler (shapes and type as squares or dots), a seeded random.

## New Color Combinations

`EXTRA_PALETTES` in `site/app.js`, review only (not in the swatch row): Midnight and mint, Black and
lime, Olive and sand, Indigo and coral. Keepers in my view: Midnight and mint and Indigo and coral (they
read as neither party and suit both branches); Olive and sand for the classic designs; Black and lime
only under Neon, Pixel state and Pixel check, where it is the point.

## Ranked, State Designs

Judged on the 40 and 60 px circles first (lesson: render at 40 before recommending), then on how far the
design is from the twelve tiles already in the row.

1. **Ribbon.** A swallowtail band holds the first word; classic, and nothing in the row does it. Reads at 40.
2. **Neon.** Hollow tube letters with a glow, the state as a tube. The strongest new look; reads at 40 and 60.
3. **Arcs.** Three bands rising over the state. The 70s civic poster; a silhouette no other tile has.
4. **Speech bubble.** The stack inside a bubble: the brand's own line (a message to friends). Reads at 40.
5. **Horizon.** The setting sun with slats and a grid. Strong at full size; the sun still reads at 60.
6. **Stencil.** Bridges cut through the letters. Clean and classic; at 40 it becomes plain Big type.
7. **Marquee.** Bulbs around a panel. Good at 40; the panel makes the type a little smaller.
8. **Blueprint.** Drafting grid, outlined state, dimension line. Quiet; the grid nearly vanishes at 40.
9. **Orbit.** Two tipped orbits and sparkles. Fun at full size, busy at 40.
10. **Pixel state.** The outline in squares. Nice large; the pixels soften to a plain map at 40.
11. **Postmark.** Dashed ring and arc text in one ink. Honest classic, but it is Ring's cousin at small sizes.
12. **Scoreboard.** Dot-matrix type. Novel, and the weakest at 40, where the dots blur.

Six to take, if the ranking holds: Ribbon, Neon, Arcs, Speech bubble, Horizon, Stencil. That gives the
state sets three classic and two futuristic looks plus the brand's own, and no two share a silhouette.

## Ranked, General Designs

1. **Done.** A check in a disc over the stack, the way an app says finished. Cleanest at every size.
2. **Wave.** Five stars over the stack, three rippling bands under it. The flag's motion without the flag.
3. **Calendar.** A page with the 3 on it. Carries the plan message on its own.
4. **Ballot box.** The ballot going into the slot. Tells the story before you read a word.
5. **Megaphone.** Tell a friend. Reads at 40.
6. **Chrome.** Polished metal type with a dark edge. The Y2K badge; needs a dark ground.
7. **Pixel check.** An 8-bit check. Simple, reads at 40.
8. **Echo.** VOTE repeated faintly behind the stack. Pleasant texture, nothing more.
9. **Dusk.** Accent fading into the ground. Good on gold and coral, flat on red.
10. **Confetti.** Lively, and the bits crowd the letters at 40.
11. **Fireworks.** The bursts shrink to specks at 40.
12. **Broadcast.** The TV is fun at full size; scanlines through the type cost legibility at 40.

Six to take: Done, Wave, Calendar, Ballot box, Megaphone, Chrome.

## The Builder

My advice is to leave the tile row as it is until the `design` dimension has two weeks of data (it was
registered October 26). The row is twelve tiles in four even rows, so adding means dropping, and we do not
yet know which current tiles earn their place. If Garrett wants new tiles anyway, the three that do a
job no current tile does: **Ribbon**, **Neon**, **Speech bubble**. Done would be the fourth, as the one
general design that still works with a state above it.

## Next Steps After Garrett Picks

1. Add the chosen state designs to `DESIGNS` in `scripts/render_bespoke.mjs` (keep the rotation step
   coprime with the new list length; `bespoke_check.py` checks variety) and give each a `LOOK` and
   `LOOK_ES` line for alt text. Re-render all 51 and look at the contact sheet before picking the home page
   eight.
2. Add the chosen general designs to the classic set (`scripts/render_classic.mjs`), with alt text per
   layout, or as a second home page set in the brand palettes. Decide whether they stay red, white and blue.
3. Move any kept palette into `PALETTES` (it then shows in the swatch row) or leave it in
   `EXTRA_PALETTES` for the sets only.
4. Rejected designs stay in `IDEAS` for a later look; the renderer additions stay either way.

## Garrett's Picks and What Shipped (October 27, Evening)

Garrett used the sheet numbers. State: 1 Postmark, 2 Marquee, 9 Pixel state. General: 2 Wave, 11 Dusk, 7 Done,
5 Calendar, kept red, white and blue. His rules for the pass: vary color and style everywhere prebuilt
stickers show, real variety from state to state, and keep every existing sticker.

- **State sets:** each state keeps its eleven bespoke stickers byte for byte (all 561 checksummed before and
  after) and gains three English ones in Postmark, Marquee and Pixel state (153 new, 714 total). Their order
  and messages turn with the state; each takes a colorway the state's English page doesn't already show,
  balanced across states (12 or 13 per colorway, Midnight, Indigo and Olive included). `bespoke_check.py`
  now wants 14 per state, all different designs, no repeated colorway on a page.
- **Classic set:** 42 per language (was 18). The four new layouts are in the classic red, navy and white, on
  navy (Wave, Calendar), red (Done) and a red-to-navy sky (Dusk), with ELECTION 2026 or ELECCIONES 2026 as the
  small line. No new white discs: lessons-learned says a light disc has no edge on a white feed, and the first
  three layouts are already white-heavy. The first six on the page are six layouts with six messages.
  The original 36 files are unchanged.
- **Home showcase:** New York went from Ring (rust, a second rust beside Texas) to I Voted Early Marquee
  (forest), Virginia from Half (plum) to Future Voter Pixel state (navy): eight designs, eight colors, and
  the set gains an I voted early.
- Pixel state: tall states drew a map 500 units tall and shrank the words; the map now stays under about
  300 units, and the date line's tracking came down so long ranges read.
