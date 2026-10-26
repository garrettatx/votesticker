# Maker: More Bespoke Stickers (Specs, Not Approved)

Written October 26, 2026, from Garrett's questions after the Varsity, Masthead and sunburst work.
Backlog only: nothing here is built. Each spec says what it applies to, what it costs, and how we'd
know it works. Mockups in this folder were rendered by the real maker code.

## Recommendations at a Glance

| # | Idea | Strength | Cost | When |
|---|---|---|---|---|
| 1 | Background finish picker (None, Sunburst, Glow, Dots, Watermark) | High: every sticker can look custom | About half a day | After Nov 5, or before if a quiet day appears |
| 2 | Pre-built bespoke sets on each state page, indexed | High for image search and for showing range | About a day with QA | After Nov 5, with the filename switch |
| 3 | Retire Shadow; fill its slot with a tightened Ballot bubble | Medium: removes the look-alike, adds a voting symbol | Two hours | Garrett's call |
| 4 | New messages: WE VOTED / YA VOTAMOS | Medium: families and couples | One hour | Any time |
| 5 | Keyline border option | Low to medium | One hour | With item 1 |
| 6 | Rename the Tilt tile to Slant | Low: avoids confusion with the tilt control | Minutes | Any time |

## 1. Background Finish Picker

**What:** replace the "Sunburst background" checkbox with a row of chips under the colors: None,
Sunburst, Glow, Dots, Watermark. Each is drawn from the sticker's own background color, so it fits
every palette.

- **Sunburst:** built (24 rays, 13 percent shade, 6 on light backgrounds).
- **Glow:** a radial gradient, lighter in the middle (the Spotlight sketch's background). Reads as an
  app icon; the strongest at 40 px of the sketches.
- **Dots:** a halftone grid of small dots fading toward the edge, like a screen-printed poster.
- **Watermark:** the state shape huge and tone-on-tone behind the words (the Watermark sketch).

**Applies to:** all 14 designs. Two need care: Sticker draws the finish inside its disc (as the
sunburst does now), and Ring needs the same treatment or its disc covers the finish. Split and Half
keep their color fields on top.

**Done when:** card QA passes for every finish, a 40 px sheet shows type still reads, and a contrast
check confirms the strongest finish tint stays under about 15 percent of the background so text
contrast doesn't drop. Remembered in the browser like the other options; file name gets the finish.

## 2. Pre-Built Bespoke Sets on Each State Page

**What:** `scripts/render_readymades.mjs` already renders each state's ready-made JPGs from the maker
code. Extend it to render six to eight curated combinations per state that only our maker produces,
like the Texas mockup (`mockup-texas-bespoke-set.png`): Varsity in state colors, Masthead, Sticker
with sunburst, Ring, the Spanish arch, FUTURE VOTER with sunburst, FIRST-TIME VOTER, a tilted Big type.

**Why:** image search is near zero (positions around 50); a page with more varied, well-described
images gives Google more to rank, and visitors see the range before they open the maker.

**Rules:**
- Descriptive file names from the first render (`texas-i-voted-varsity-state-colors.jpg`), so these
  never need the redirect step the current images do (`build/image_names.py`).
- Alt text generated from the combination, same as Copy image description.
- Evergreen messages only (I VOTED, YA VOTÉ, FUTURE VOTER, FIRST-TIME VOTER, VOTE NOV 3 with a year),
  so the set doesn't expire; no date ranges.
- Combination rules per state rather than hand-picked: state colors on Varsity, the state's best
  contrast palette on Masthead, one Spanish sticker per state.
- A review sheet of all 51 sets before publishing.

**Placement:** a "More designs" row under the ready-made gallery, lazy-loaded, linking each to the
maker preset to that combination. 51 x 8 = 408 images; image sitemap entries added automatically.

**Timing:** images take days to weeks to show in image search, so this pays off for post-election
"I voted" searches and 2028, not this week. Build it with the filename switch after Nov 5.

## 3. Big Type, Shadow and Varsity

The comparison (`mockup-bigtype-shadow-varsity.png`) shows the overlap: all three are the same
centered condensed stack; Shadow and Varsity both add an offset copy, at two strengths.

**Recommendation:** keep Big type (the clean, most legible base) and Varsity (the bold one). Retire
Shadow: Varsity does what it does, better. To keep 14 tiles, replace it with the Ballot bubble sketch,
tightened so the words get bigger at 40 px (move the oval above the words, or shrink it to the size of
one line). The bubble is the only voting symbol in the row.

**Care:** a visitor with Shadow saved in the browser falls back to the first design; check that path.
Shadow has no ready-made images, so nothing published changes.

## 4. WE VOTED / YA VOTAMOS

Two new messages for couples and families posting one photo: WE / VOTED and YA / VOTAMOS (both lines
10 characters or fewer). State and year on the date bar like I VOTED. Nonpartisan, true after voting.

## 5. Keyline Border

A thin ring just inside the circle in the ink color, on any design: the cheapest way to make a
sticker feel finished. Off by default; hidden for Sticker and Ring, which already have rings.

## 6. Tilt Naming

The tilt control (Straight, Left, Right) turns the whole sticker; the Tilt design tips only the
headline. Two things called "tilt" is confusing. Rename the design tile to Slant (Inclinado stays).
Don't retire it: it's the default design and the house favorite.

## What We Learned Doing This (Applied Above)

See `docs/lessons-learned.md`: render options at 40 px before recommending them, test any option
against every design, and name new images descriptively from the start.
