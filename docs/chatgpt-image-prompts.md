# ChatGPT Image Prompts for Hand-Made State Sets

Written October 19, 2026 for Garrett's friends' states, to make sets like "More Texas Designs."
Dates and messages match `site/states.js` as verified that day. If a state's row changes, change its
prompt before making more images.

**How to run it:** one ChatGPT chat per batch. Paste the setup message with Texas images 1, 2 and 6
attached (`site/assets/texas/`), wait for "Ready," then send one state at a time and say "next layout"
for the rest of its set.

**Before keeping an image:** check spelling, the en dash in ranges, the state outline (Long Island,
Outer Banks and Puget Sound go wrong most), that nothing sits outside the center circle, and that it
reads at about 50 px. Save keepers to `~/Downloads/vote-early/<state>/`; Claude adds a "More [State]
Designs" section for any state with images and runs the crop check on them first.

## Setup Message

```
I'm making nonpartisan "vote" profile pictures for friends in different states. The attached Texas images are the style to match. Follow these rules for every image I ask for in this chat:

FORMAT
- Square, 1:1, at least 1080 x 1080 px. One image per reply.
- Profile pictures get cropped to a circle. Keep ALL text and the state shape inside a centered circle about 85% of the image width. Corners hold only background color. Nothing touches or nears the edges.

TEXT
- Use only the exact words I give you: a two-line headline and one date line. No other words, no URL, no hashtags, no state name unless I ask for it.
- Spell everything exactly as given, all caps. Date ranges use an en dash (OCT 24–NOV 1), never a hyphen or em dash.
- Headline in a heavy condensed sans like Anton or Bebas Neue Bold; date line in a wide heavy sans like Archivo Black. Big type: the headline should fill most of the circle's width.
- It must read clearly at 48 px wide, so keep it big and simple.

ART
- Flat vector style like the Texas set: 2 or 3 solid colors, high contrast, no gradients, textures, shadows, glows or 3D.
- The state outline must be geographically accurate and instantly recognizable. A small five-point star marks the state capital in the right spot.
- No people, ballots, checkmarks, flags, party symbols, donkeys, elephants, candidate names, or logos. Keep it nonpartisan: don't lean on red-versus-blue as a theme.

PALETTES (pick the one I name)
- Cream: background #FBF8F0, ink #14295E, accent #C1272D
- Navy: background #0F2558, ink #FBF6E8, accent #B3262C
- Forest: background #0A3D2C, ink #FBF6E8, accent #D4AF4A
- Sand: background #EBDABF, ink #1F1E1C, accent #A9552A
- Sky: background #D4E8FA, ink #14295E, accent #C1272D
- Sage: background #8FAF88, ink #0B3A23, accent #E0BC4E

LAYOUTS (I'll ask for these by name)
- Shape: big state outline in ink, headline knocked out in background color across it, star on the capital, date line below the shape.
- Big type: small state outline on top, huge two-line headline, date line in a solid accent-color bar.
- Badge: thin ring just inside the crop circle, small state outline, headline, date in an accent bar.

Reply "Ready" and wait for the first state.
```

## States

### Kansas

```
Kansas. Headline: VOTE / EARLY. Date line: BY NOV 2. Capital star: Topeka.
Make 4 images, one at a time: Shape in Cream, Big type in Navy, Badge in Forest, and Shape in a Kansas palette: background #F4E9C8, ink #1F3A5F, accent #E0A526 (sunflower gold).
```

### Missouri

```
Missouri. Headline: VOTE / EARLY. Date line: OCT 20–NOV 2. Capital star: Jefferson City.
Make 4 images, one at a time: Shape in Cream, Big type in Navy, Badge in Sand, and Shape in a Missouri palette: background #DCE9F2, ink #12355B, accent #2E7D6B (river green).
```

### New York

```
New York. Headline: VOTE / EARLY. Date line: OCT 24–NOV 1. Capital star: Albany.
The outline must include Long Island. Make 4 images, one at a time: Shape in Cream, Big type in Navy, Badge in Sky, and Big type in a New York palette: background #1B1B1B, ink #FBF6E8, accent #F2A900 (taxi yellow).
```

### Washington

```
Washington. Headline: VOTE / BY MAIL. Date line: DUE NOV 3. Capital star: Olympia.
Washington votes by mail, so never use the word "early." The outline must show Puget Sound clearly. Make 4 images, one at a time: Shape in Forest, Big type in Navy, Badge in Sage, and Shape in a Washington palette: background #E3EEE6, ink #1E4D3A, accent #3E7CB1 (Sound blue).
```

### California

```
California. Headline: VOTE / BY MAIL. Date line: DUE NOV 3. Capital star: Sacramento.
Make 4 images, one at a time: Shape in Cream, Big type in Navy, Badge in Sand, and Shape in a California palette: background #FFF3E0, ink #1F3A5F, accent #F28C28 (poppy orange).
```

### Colorado

```
Colorado. Headline: VOTE / BY MAIL. Date line: DUE NOV 3. Capital star: Denver.
Colorado's outline is a plain rectangle, so make it bold and add one subtle flat mountain-ridge line inside the bottom of the shape, without crossing the text. Make 4 images, one at a time: Shape in Sky, Big type in Navy, Badge in Forest, and Big type in a Colorado palette: background #1D3557, ink #F1FAEE, accent #F4B942 (aspen gold).
```

### Virginia

```
Virginia. Headline: VOTE / EARLY. Date line: BY OCT 31. Capital star: Richmond.
The outline must include the Eastern Shore peninsula. Make 4 images, one at a time: Shape in Cream, Big type in Navy, Badge in Forest, and Shape in a Virginia palette: background #EDE6DA, ink #233142, accent #8C2F39 (brick).
```

Early voting started September 18, so "BY OCT 31" is more useful now than the full range the page shows.

### Washington, DC

```
Washington, DC. Headline: VOTE / EARLY. Date line: OCT 26–NOV 1.
Instead of an outline and capital star, use the three-stars-over-two-bars motif from the DC flag, small, above the headline. Make 4 images, one at a time: Big type in Cream, Big type in Navy, Badge in Cream, and Badge in a DC palette: background #FFFFFF, ink #1B1B1B, accent #C8102E.
```

### North Carolina

```
North Carolina. Headline: VOTE / EARLY. Date line: OCT 15–31. Capital star: Raleigh.
The outline must show the Outer Banks. Make 4 images, one at a time: Shape in Cream, Big type in Navy, Badge in Sage, and Shape in a North Carolina palette: background #E6F0F8, ink #13294B, accent #4B9CD3 (Carolina blue).
```

### Pennsylvania

```
Pennsylvania. Headline: VOTE / EARLY. Date line: BY OCT 27. Capital star: Harrisburg.
Make 4 images, one at a time: Shape in Cream, Big type in Navy, Badge in Forest, and Big type in a Pennsylvania palette: background #102A43, ink #F0F4F8, accent #F0B429 (keystone gold).
```

Pennsylvania's "early voting" is filling out a mail ballot at the county office through 5 p.m. October 27; "VOTE EARLY / BY OCT 27" matches how the state describes it.

## Round 2 (October 19, 2026, afternoon)

Next six states: California, Colorado, Virginia, DC, North Carolina, Pennsylvania. Lessons from round 1:
New York lost 3 of 10 to stretched outlines, and Missouri's star drifted to St. Louis in 4 of 10.

### Rules Message (send once, before the next state)

```
Two more rules for every image from now on:
- Never stretch or squash the state outline to fit the text. Keep its true proportions; make the text smaller or move it instead.
- Put the capital star where the capital actually is, not on a border or a big city.
When a state's set is done, give me all 10 as a zip named [State]-[Message]-10-Profile-Pictures, with files named [state]-01-[layout]-[palette].png through -10.
```

### State Messages (first 4 images)

The prompts above for California, Colorado, Virginia, DC, North Carolina, and Pennsylvania, with
these additions: California and Colorado add "mails every voter a ballot, so never use the word
'early'"; California adds "tall and thin, so in Shape layouts set the headline beside or below the
outline rather than stretching the state"; North Carolina adds "wide and short, so in Shape layouts put
the headline below or across the shape at a size that keeps the outline's true proportions."

### Six More Per State (creative freedom)

```
Now make 6 more for this state, one at a time, with more creative freedom. The goal: a profile picture that makes a friend scrolling past stop and check when they can vote, and that a person in this state would be proud to use.

You can change: the layout, the palette (any 2 or 3 flat colors with strong contrast, including colors that feel like this state's landscape, seasons, or landmarks), the type arrangement, and small flat touches tied to the state (a skyline line, a mountain ridge, a coastline, a state flower or bird in silhouette).

Keep fixed:
- The exact headline and date line, spelled exactly, nothing added.
- Everything inside the centered circle, about 85% of the width. Corners are background only.
- Readable at 48 px: the headline stays the biggest thing in the image.
- Flat vector, no gradients, photos, textures, or 3D.
- An accurate outline at its true proportions, or no outline at all.
- Nonpartisan: no flags (except DC's motif), party symbols, red-versus-blue themes, ballots, checkmarks, or people.

Make the 6 different from each other and from the first 4.
```

## Round 3 (October 19, 2026): One Prompt, Six States, One Zip Each

**Add to the prompt before sending (learned from round 2, where 15 of 60 were rejected):** "Never set the
headline inside the state outline. Keep the outline small above or beside the text, or leave it out."

Florida, Michigan, New Jersey, Maryland, Minnesota, Oregon: the most-populous states with verified
dates after round 2. Self-contained; start a new chat with 2 or 3 Texas images attached.

```
I'm making nonpartisan "vote" profile pictures for friends in different states. The attached Texas images show the style. You'll make 10 images per state for 6 states, one state at a time.

WORKFLOW
- Work through the states in the order listed. For each state: make its 10 images one at a time, then give me a zip of that state's 10 PNGs before starting the next state.
- Zip name: [State]-[Message]-10-Profile-Pictures (for example Florida-Vote-Early-10-Profile-Pictures). File names: [state]-01-[layout]-[palette].png through -10, lowercase with hyphens.
- Don't wait for me between states. If you run out of room, stop after a finished zip and tell me which state is next.

FORMAT (every image)
- Square, 1:1, at least 1080 x 1080 px.
- Profile pictures get cropped to a circle. Keep ALL text and art inside a centered circle about 85% of the image width. Corners hold only background color. Nothing touches or nears the edges.

TEXT
- Use only the exact headline and date line given for the state. No other words, no URL, no hashtags, no state name.
- All caps, spelled exactly. Date ranges use an en dash (OCT 24–NOV 1), never a hyphen or em dash.
- Headline in a heavy condensed sans like Anton; date line in a wide heavy sans like Archivo Black.
- It must read at 48 px wide: the headline is always the biggest thing in the image.

ART
- Flat vector, 2 or 3 solid colors, high contrast. No gradients, textures, shadows, photos, or 3D.
- The state outline must be accurate at its true proportions. Never stretch or squash it to fit text; make the text smaller or move it instead.
- A small five-point star marks the capital at its real location, not on a border or a big city.
- Nonpartisan: no flags, party symbols, red-versus-blue themes, ballots, checkmarks, people, or logos.

PALETTES
- Cream: background #FBF8F0, ink #14295E, accent #C1272D
- Navy: background #0F2558, ink #FBF6E8, accent #B3262C
- Forest: background #0A3D2C, ink #FBF6E8, accent #D4AF4A
- Sand: background #EBDABF, ink #1F1E1C, accent #A9552A
- Sky: background #D4E8FA, ink #14295E, accent #C1272D
- Sage: background #8FAF88, ink #0B3A23, accent #E0BC4E

LAYOUTS
- Shape: big state outline in ink, headline knocked out in background color across it, star on the capital, date line below.
- Big type: small state outline on top, huge two-line headline, date line in a solid accent-color bar.
- Badge: thin ring just inside the crop circle, small state outline, headline, date in an accent bar.

PER STATE: 10 IMAGES
Images 1 to 4: Shape in Cream, Big type in Navy, Badge in Forest, and Shape in the state's own palette (listed below).
Images 5 to 10: six more with creative freedom. The goal is a picture that makes a friend scrolling past stop and check when they can vote, and that someone from this state would be proud to use. You can change the layout, palette (any 2 or 3 flat colors with strong contrast, including colors from the state's landscape or seasons), type arrangement, and add small flat touches tied to the state (a coastline, a ridge line, a skyline line, a state flower or bird in silhouette). Everything in FORMAT, TEXT, and ART still applies. Make the six different from each other and from images 1 to 4.

STATES
1. Florida. Headline: VOTE / EARLY. Date line: OCT 24–31. Capital star: Tallahassee. Include the panhandle at true length. State palette: background #E8F6F3, ink #0B4F6C, accent #F28F3B.
2. Michigan. Headline: VOTE / EARLY. Date line: OCT 24–NOV 1. Capital star: Lansing. Show both the Upper and Lower Peninsulas. State palette: background #E9F1F7, ink #0B3954, accent #2A9D8F.
3. New Jersey. Headline: VOTE / EARLY. Date line: OCT 24–NOV 1. Capital star: Trenton. State palette: background #F1ECE2, ink #1B2A41, accent #C9A227.
4. Maryland. Headline: VOTE / EARLY. Date line: OCT 22–29. Capital star: Annapolis. Keep the western panhandle and the Chesapeake Bay shape accurate. State palette: background #F6F1E7, ink #1C1C1C, accent #E0A100.
5. Minnesota. Headline: VOTE / EARLY. Date line: BY NOV 2. Capital star: Saint Paul. Include the small Northwest Angle notch at the top. State palette: background #EAF2F8, ink #102A43, accent #3C8D5A.
6. Oregon. Headline: VOTE / BY MAIL. Date line: DUE NOV 3. Capital star: Salem. Oregon mails every voter a ballot, so never use the word "early." State palette: background #E6EFE9, ink #1F3B2C, accent #2F6690.

Start with Florida.
```
