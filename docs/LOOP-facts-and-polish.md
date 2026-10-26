# Loop: Facts and Polish

Started October 26, 2026, after the final-week loop (`docs/LOOP-final-week.md`) finished its seven
items the same morning. The election prep loop (`docs/LOOP-election-prep.md`) keeps the dated checks;
run them first when their date comes. This loop fills the most useful missing facts before Election
Day, checks the accessibility the About page now promises, then takes the maker items left in the
backlog. One item per pass; log each pass at the bottom.

**Goal of every item:** a voter who lands on a page gets something true and useful for that day.
Nothing expired, nothing invented, nothing uploaded, nothing partisan.

**Rules** are the election prep loop's: two or three pushes a day; facts, SEO, Spanish
(`--base http://127.0.0.1:8792/`), card, save and event QA before a push; official sources only, with
an exact quote read from the page itself (curl or Chrome, not a summary); no DNS, Cloudflare, social
posts or outreach without Garrett's OK; no Firecrawl; Spanish follows `docs/spanish-style.md`; the
build fails on any PHP warning. Research agents (two at most) report sources and quotes; they don't
edit files, and every quote is re-read before it goes on a page.

## Items

| # | Item | Why | Status |
|---|---|---|---|
| 1 | **"In line when polls close" for the 19 states without it** (`polls_rules.line`: DC FL GA IA IL MD MT NC ND NH NM SD TX UT VA VT WA WV WY) | The most useful Election Day sentence, and missing for TX, FL, GA and NC | |
| 2 | **Keyboard-only walkthrough of the maker and state pages** | About now says the controls work with a keyboard; prove it or fix it | Done Oct 26: passes, no change |
| 3 | **Sticker story for the 18 states without one** (`sticker.tradition`: AR AZ CA DC DE IA IL IN MA ME NE NJ NV PA SC TX VA WI), official sources only | The sticker is the brand, and the big states have nothing | |
| 4 | **Tilt control** (Straight, Left, Right) under the swatches; `render` already takes `tilt` | Cheap design range from the October 22 work | |
| 5 | **"Future voter"** message for kids and families, EN and ES | Useful on Election Day and after it | |
| 6 | **Guide type:** body text 17 px, line height about 1.6, in the guide sections | Backlog review item; long FAQ answers on phones | |

## Log

- **Oct 26:** loop written. Items 1 and 3 research started with two agents; item 2 runs meanwhile.
- **Oct 26, item 2 (keyboard).** Playwright, Tab only, 220 presses through the Texas page at 1280 px:
  every control reached (state finder, gallery and design tiles, swatches, size buttons, message
  select, date line, Download, Open full image, Copy image description, post chips and text, Copy post
  text, calendar link, share), and every one shows a focus outline or ring. Enter selects a design
  tile, Space a swatch, and the preview redraws. On the home page, typing "ohi", Down and Enter opens
  /ohio/. No change needed. Not tested here: a real screen reader (VoiceOver), which needs a person.
