# Cover Photos and Maker Copy (Specs, Not Approved)

Written October 26, 2026, from an outside review of the maker (PDF of the live builder) that Garrett
shared. Backlog only. Each part says what we'd keep from the review and where we'd differ.

## 1. Cover Photo Format

**What:** a fourth output, "Cover photo," with its own compositions; a sticker is never stretched
into a banner.

**Placements and sizes**, stored as data with the date each was checked against the platform's own
help page:

| Placement | Export | Notes |
|---|---|---|
| LinkedIn profile | 1584 x 396 | Profile photo overlaps the lower left; crop varies with window size (LinkedIn Help) |
| X header | 1500 x 500 | Avatar overlaps lower left; top and bottom can crop (X Help Center) |
| Facebook Page | 851 x 315 shown; upload larger | Profile picture covers part of the left; mobile crops the sides (Facebook Help) |
| Facebook profile | Separate preset | Test before shipping; not the same as a Page |

Re-read each help page before building: the numbers above come from those pages via the review and
haven't been checked here yet.

**Safe area:** for each preset, the text and state art go in the intersection of the desktop and
mobile visible areas, minus the profile-picture overlap. If the message doesn't fit at a readable
size, switch to a shorter layout rather than shrink.

**Compositions** (start with one, test it end to end on Facebook, then add the rest):
1. Message beside the state artwork, art on the right, left third kept clear (the Side by side lockup
   fitted to a rectangle; `fit()` needs a rectangular mode next to the circle one).
2. Message on a color band across the middle with the state watermark behind.
3. Minimal: message only, over the sunburst or glow finish.

**Preview:** the cover with a shaded profile-picture overlap and the crop edges, Desktop and Mobile
toggles, and the line "Preview is approximate. Check placement after uploading." Guides never appear
in the saved file. Never promise "nothing gets cut off."

**Not in scope:** photo upload or background removal (see section 3).

**Done when:** each preset renders every message in both languages with no text in the overlap or
crop zones (an automated check like the card QA's safe circle, but rectangular), and a real upload
to each platform has been checked by a person.

## 2. Maker Copy and Grouping

Keep from the review:

| Now | Change to |
|---|---|
| Square / Portrait / Story | Profile picture / Post / Story (sizes as small print) |
| Straight / Left / Right | Level / Tilt left / Tilt right |
| Preview circle crop | Show profile-picture crop |
| Round sticker with a see-through background (PNG) | Transparent PNG, no background |
| Copy post text | Copy caption + link |
| Suggested post text | Caption to share with your sticker |
| Plan / Short / Friends / Official info chips | Outcome labels (for example Celebrate, Remind friends, Voting info); analytics ids stay `plan`, `short`, `friends`, `official` |
| Reset (date line) | Reset date line |
| Masthead, Half | Headline, Two-tone |

Group the controls into Message, Layout, Appearance (colors, background finish, angle) and Save.

Where we differ:
- **"Save to Photos" stays on iPhone.** It opens the share sheet, where Save Image goes to Photos; a
  plain download goes to Files (October 22 fix, reference_ios-download-goes-to-files). Android and
  desktop already say Download picture.
- **Other design names stay.** The thumbnails show the current message in each layout; Varsity, Ring,
  Brush and the rest are clear once seen. Only rename where a name misleads.
- **The date line is already labeled as editable** and says when the text differs from the verified
  dates; check the wording before changing it.

## 3. Photo Upload: Garrett's Call

The review assumes an uploaded photo. The site's promise today is that it never asks for a photo and
nothing is uploaded. A photo feature, even one processed only in the browser, changes what that
promise means to a visitor. Decide first; if yes, it gets its own spec (a bounded photo panel, never
text over a face, no background removal in the first version).
