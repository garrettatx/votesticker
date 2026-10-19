# Round 3 Prompts: One State at a Time

Written October 19, 2026, after ChatGPT stalled on the six-state prompt. Same six states, same dates
(`site/states.js`). Start a new chat, attach 2 or 3 Texas images, send the setup message, then send
one state message. Send the next state only after that state's zip arrives. Two states per chat is the
most to try; start a fresh chat after that.

The Shape layout changed: round 2 lost 15 of 60 images to headlines set inside the state outline, so
text and outline never overlap now.

## 1. Setup Message (Once per Chat)

```
I'm making nonpartisan "vote" profile pictures for friends in different states. The attached Texas images show the style. I'll send one state at a time. For each state you make 10 images, check them, and give me one zip. Then stop and wait for the next state.

FORMAT (every image)
- Square, 1:1, at least 1080 x 1080 px.
- Profile pictures get cropped to a circle. Keep ALL text and art inside a centered circle about 85% of the image width. Corners hold only background color. Nothing touches or nears the edges.

TEXT
- Use only the exact headline and date line I give for the state. No other words, no URL, no hashtags, no state name.
- All caps, spelled exactly. Date ranges use an en dash (OCT 24–NOV 1), never a hyphen or em dash.
- Headline in a heavy condensed sans like Anton; date line in a wide heavy sans like Archivo Black.
- It must read at 48 px wide: the headline is always the biggest thing in the image.

ART
- Flat vector, 2 or 3 solid colors, high contrast. No gradients, textures, shadows, photos, or 3D.
- Never put the headline inside the state outline, and never let text overlap the outline. Keep the outline above, beside, or below the text, or leave it out.
- The outline must be accurate at its true proportions. Never stretch or squash it; make it smaller instead.
- A small five-point star marks the capital at its real location. The star never touches or cuts into a letter.
- Nonpartisan: no flags, party symbols, red-versus-blue themes, ballots, checkmarks, people, or logos.

PALETTES
- Cream: background #FBF8F0, ink #14295E, accent #C1272D
- Navy: background #0F2558, ink #FBF6E8, accent #B3262C
- Forest: background #0A3D2C, ink #FBF6E8, accent #D4AF4A
- Sand: background #EBDABF, ink #1F1E1C, accent #A9552A
- Sky: background #D4E8FA, ink #14295E, accent #C1272D
- Sage: background #8FAF88, ink #0B3A23, accent #E0BC4E

LAYOUTS
- Shape: medium state outline in ink in the upper part of the circle, capital star on it, headline below the outline (not touching it), date line in an accent bar at the bottom.
- Big type: small state outline on top, huge two-line headline, date line in a solid accent-color bar.
- Badge: thin ring just inside the crop circle, small state outline, headline, date in an accent bar.

IMAGES 1 TO 4 (guidelines)
Shape in Cream, Big type in Navy, Badge in Forest, and Shape in the state's own palette.

IMAGES 5 TO 10 (creative)
Six more with creative freedom. The goal is a picture that makes a friend scrolling past stop and check when they can vote, and that someone from this state would be proud to use. You can change the layout, the palette (any 2 or 3 flat colors with strong contrast, including colors from the state's landscape or seasons), and the type arrangement, and you can add one small flat touch tied to the state (a coastline, a ridge line, a skyline line, a state flower or bird in silhouette). Everything in FORMAT, TEXT, and ART still applies. Make the six different from each other and from images 1 to 4.

BEFORE THE ZIP: CHECK ALL 10
Look at each image again and redo any that fails:
1. Headline and date line spelled exactly as given, nothing added, en dash in ranges.
2. Everything inside the center circle; corners are plain background.
3. Text never overlaps the state outline; the star doesn't touch a letter.
4. Outline accurate and not stretched; star on the capital, not a big city or border.
5. Headline still readable shrunk to 48 px.
6. No two images look nearly the same.
Then tell me "10 of 10 checked" and list any you redid and why.

THE ZIP
- One zip per state named [State]-[Message]-10-Profile-Pictures (for example Florida-Vote-Early-10-Profile-Pictures).
- Inside: exactly 10 PNGs named [state]-01-[layout]-[palette].png through [state]-10-..., lowercase with hyphens.
- After the zip, list the 10 file names it holds, then stop and wait for my next state.

Reply "Ready" and wait for the first state.
```

## 2. State Messages (One per Turn)

Send one. Wait for its zip and the file list before sending the next.

### Florida

```
Florida. Headline: VOTE / EARLY. Date line: OCT 24–31. Capital star: Tallahassee. Include the panhandle at its true length; Florida is wide at the top, so keep the outline small rather than squashing it. State palette: background #E8F6F3, ink #0B4F6C, accent #F28F3B. Make the 10, check them, then send the zip.
```

### Michigan

```
Michigan. Headline: VOTE / EARLY. Date line: OCT 24–NOV 1. Capital star: Lansing. Show both the Upper and Lower Peninsulas, with the gap between them. State palette: background #E9F1F7, ink #0B3954, accent #2A9D8F. Make the 10, check them, then send the zip.
```

### New Jersey

```
New Jersey. Headline: VOTE / EARLY. Date line: OCT 24–NOV 1. Capital star: Trenton, on the western edge along the Delaware River. New Jersey is tall and narrow, so set the headline beside or below it, never inside. State palette: background #F1ECE2, ink #1B2A41, accent #C9A227. Make the 10, check them, then send the zip.
```

### Maryland

```
Maryland. Headline: VOTE / EARLY. Date line: OCT 22–29. Capital star: Annapolis, on the west shore of the Chesapeake Bay. Keep the long western panhandle and the bay shape accurate; Maryland is very wide and thin, so keep the outline small above the headline. State palette: background #F6F1E7, ink #1C1C1C, accent #E0A100. Make the 10, check them, then send the zip.
```

### Minnesota

```
Minnesota. Headline: VOTE / EARLY. Date line: BY NOV 2. Capital star: Saint Paul, in the east-central part of the state, not Minneapolis or Duluth. Include the small Northwest Angle notch at the top and the Arrowhead in the northeast. State palette: background #EAF2F8, ink #102A43, accent #3C8D5A. Make the 10, check them, then send the zip.
```

### Oregon

```
Oregon. Headline: VOTE / BY MAIL. Date line: DUE NOV 3. Capital star: Salem, in the northwest, not Portland. Oregon mails every voter a ballot, so never use the word "early" anywhere. State palette: background #E6EFE9, ink #1F3B2C, accent #2F6690. Make the 10, check them, then send the zip.
```

## 3. If ChatGPT Stalls

```
Stop there. Send the zip of the images you've finished and checked so far, list the file names, and tell me which numbers are still missing.
```

Then start a new chat, send the setup message again, and ask for only the missing numbers:
"Florida again, images 7 to 10 only, same rules."
