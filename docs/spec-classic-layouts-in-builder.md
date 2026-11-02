# Spec: Classic Layouts in the Sticker Builder

Status: **built November 2** (Garrett: go ahead, in a loop; see the LOOP log). Originally a spec. Written November 2, 2026 from Garrett's five favorites in the classic set: I voted
by mail and I voted early in the **Wave** layout, First-time voter in the **Star ring**, I voted in **Stars and
stripes**, and I voted in **Dusk**. Aim: three classic options in the builder's layout row.

## What Exists

All five are drawn by `site/app.js` layouts the classic renderer (`scripts/render_classic.mjs`) already uses:

| Favorite | Layout id | In `DESIGNS` today | Takes the palette | Takes a date or state line |
|---|---|---|---|---|
| I voted by mail, I voted early (waves) | `wave` | No, in `IDEAS` | Yes | Yes (`dateText`) |
| First-time voter (star ring) | `cring` | Yes, retired from the row | No, fixed red, white and blue | No |
| I voted (stars and stripes) | `cflag` | Yes, retired from the row | No, fixed | No |
| I voted (dusk) | `dusk` | No, in `IDEAS` | Yes | Yes |

The row has 12 tiles in four even rows (October 26). Three more makes 15: five rows of three on a phone.

## Pick

**Wave, Star ring, Stars and stripes.** They're the three most like a printed poll-site sticker, and they look
different from each other at tile size (bands, a ring, a flag). Dusk is a gradient night sky, closer to the
modern tiles already in the row; keep it as the alternate if one of the three fails the fit checks.

## Behavior

- **A "Classic" label on the three tiles** (or a thin divider before them), so the row reads as 12 state
  designs plus 3 classics.
- **Colors.** Star ring and Stars and stripes are red, white and blue by definition. When one is picked, the
  color swatches are replaced by one line: "Classic red, white and blue." Wave keeps the palette but defaults
  to the classic navy with red and white waves, the version Garrett picked.
- **State and date line.** The builder promises your state's name. Wave already has a bottom line ("ELECTION
  2026" in the classic set); in the builder it takes the dates field ("TEXAS 2026" by default). Star ring and
  Stars and stripes need a small line added: under the message inside the white disc for Star ring, and in the
  lower red stripe area for Stars and stripes, at the same size as the classic "ELECTION 2026". Empty field: no line.
- **Messages.** Every builder message, English and Spanish, has to fit all three, including the long ones
  ("I voted by mail", "Yo voté por correo", "First-time voter", "Mi primer voto"). `bandText` and `ringBlock`
  already fit two lines; check the longest.
- **Saves.** Same Download/Save to Photos path, the same `sticker_save` event with `design` set to the layout
  id, so GA4 shows whether classics get picked.

## QA

- Render every message x the three layouts x both languages x the three picture sizes into a contact sheet
  (`scripts/sheet_ideas.mjs` already does this for `IDEAS`) and read it.
- `qa_cards.mjs`: no text outside the safe circle, profile-picture crop intact, the dates line fits at its
  longest default (states with two-word names plus the year).
- Builder at 320, 390, 860 and 1280: the 15-tile row wraps evenly and the swatch line swaps in and out without
  moving the layout.
- Plant: a message one line too long must fail the fit check.

## Size and Timing

About two to three hours with the contact-sheet review: the new line on two layouts is most of it. Build after
Election Day (November 4 or later): the builder is the most-used tool tomorrow, and the post-election message
set is the one these would ship into.
