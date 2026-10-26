# Lessons Learned

Started October 26, 2026. One line of what happened, then the rule. Newest first.

## Design and the Maker

- **Render at 40 px before recommending a design.** Rays and Tricolor looked strong as described and
  as squares; at 40 px Rays read like our existing Sticker and Ring, and Tricolor's bands cropped to
  slivers. Every design proposal now comes with a 40 px sheet (`scripts/sheet_new.mjs`).
- **Test a new option against every design, not the default.** The sunburst was invisible on
  Sticker (its disc covered it) and gray and busy on cream backgrounds; both showed up only on the
  full 14-tile grid. Screenshot the whole grid with the option on.
- **A web font can load and still not draw.** Google Fonts splits a face into subsets; loading one
  file with `FontFace` and no unicode range gave us the Vietnamese subset, `load()` resolved, and the
  canvas silently fell back to a system serif. Check by measuring a word's width against the
  fallback, and self-host the latin file.
- **Look-alikes creep in one design at a time.** Big type, Shadow and Varsity each made sense when
  added; side by side they are one idea at three strengths. Compare a new design against the whole row.
- **Remembered options outlive the visit.** Round cut-out, sunburst and tilt are saved in the
  browser, so a returning visitor's previews start with them on. Make the saved state visible in the
  controls (it is) and never let a saved option hide a design.

- **Make generated sets reproducible from a manifest.** The bespoke renderer writes the full recipe of
  every file (design, palette, message, tilt, sunburst, language) to `bespoke.json`; the homepage
  picks, the post-election hero and the rule check all read it instead of guessing from file names.
- **Turn the rules into a build check, then plant a violation.** "Voted early only where verified" is
  easy to say and easy to break in a rotation; `bespoke_check.py` catches it, and three planted errors
  proved it does.
- **Check a rotation's cycle against the list length.** A step of 3 through 9 palettes repeats every
  third slot; the sets looked varied in one contact sheet and weren't. Use a step that's coprime with
  the list length, and put the variety rule in the build check.
- **Pick showcase images from a contact sheet, not from the recipe.** The rotation makes good variety
  on average; the home page needs the best eight, chosen by looking at all of them.
- **Count files, not matches.** A gallery count doubled because the regex hit each image's link and
  its img; parse one element per file before raising an alarm.

## Facts and Content

- **Agents report; we re-read.** Every quote from a research agent was re-fetched and matched word
  for word before it went on a page. That caught a calendar line cited for Rhode Island's cure deadline
  that was really about military ballots; the date was then worked out from the rule itself.
- **Dated text needs its after-text.** A deadline sentence without `until` and past-tense text goes
  stale the day after; the Nov 17 preview found none left after adding them.
- **Name images for people from the first render.** The 477 ready-mades carry numbered names and need
  a redirect map to fix; new image sets get descriptive names on day one.

## Build and Deploy

- **Guard what the build can't see.** PHP warnings sat in a data attribute on four state pages for
  five days. The build now stops on any PHP warning, and on a calendar file without its links.
- **A date is too coarse to detect a change.** IndexNow compared sitemap dates, so a second deploy on
  the same day looked unchanged and sent nothing. Compare content fingerprints, and read the build log's
  "to submit" count after every deploy.
- **GitHub's scheduled runs start hours late.** The 6:05 a.m. build ran 3 to 5 hours late all week;
  on days that matter (Nov 2, Nov 4), dispatch by hand by 7 a.m.
