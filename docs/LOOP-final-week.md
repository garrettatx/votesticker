# Loop: The Final Week

Started October 26, 2026, after the election prep loop (`docs/LOOP-election-prep.md`) finished its
numbered items. That file keeps its dated checks (daily builds through Nov 1, Oct 29 Search Console,
Nov 1 index snapshot, Nov 2 banner, Nov 4 post mode, Nov 5 cron removal); run them first when their
date comes. This loop takes the backlog items that help a voter before November 3, in order of value.
One item per pass; log each pass at the bottom.

**Goal of every item:** a voter who lands on a page gets something true and useful for that day.
Nothing expired, nothing invented, nothing uploaded, nothing partisan.

**Rules** are the election prep loop's: two or three pushes a day at most, after facts, SEO, Spanish
(`--base http://127.0.0.1:8792/`), card and event QA; official sources only; no DNS, Cloudflare, social
posts or outreach without Garrett's OK; no Firecrawl; Spanish follows `docs/spanish-style.md`; the
build fails on any PHP warning. A feature that only works before Election Day must switch off or
change by itself in the Nov 2 (`eday`) and Nov 4 (`post`) phases; check both with `VOTE_TODAY`
preview builds, then restore `build/lastmod.json`.

## Items

| # | Item | Why | Status |
|---|---|---|---|
| 1 | **Add to calendar.** A per-state `.ics` file written by the daily build for the voter's days: Election Day, or the last day of early voting or the mail deadline where verified. Official link in the event; no poll hours unless verified for the whole state. Nothing stored or sent. | A plan with a date on it is the step people skip | Done Oct 26 |
| 2 | **"Early voting ends in N days"** on state pages with a verified end date, recounted by the visitor's calendar like the Election Day countdown; gone after the end date | The most useful line in the last week, and the one most likely to go stale if hand-written | Done Oct 26 |
| 3 | **Profile-picture size preview.** A 48 px circle beside the big preview in the maker | People can check the sticker reads where friends will see it | Done Oct 26 |
| 4 | **Copy alt text.** A button that copies a plain description of the current sticker | For people who add alt text to posts; the About page now promises accessibility | Done Oct 26 |
| 5 | **"Send this to someone who hasn't voted yet."** After an I voted sticker is saved, a share-link line (the page link only, no tracking beyond the existing `share` event) | Turns a saved sticker into one more reminder | |
| 6 | **Contrast and tap-target check.** `--muted` on cream against WCAG AA; every control 44 px or larger | Backlog review item; the About page claims keyboard and focus support | |
| 7 | **Portrait 1080 x 1350** beside square and story | Instagram feed posts crop square stickers less well | |

## Log

- **Oct 26:** loop written. Next: item 1, then the dated live check once today's build lands.
- **Oct 26, item 1 (Add to calendar).** Every state panel (EN and ES) has an "Add to my calendar"
  button linking to `/cal/<state>.ics` (`/es/cal/`), written by the build for its own date through a
  new `render.php cal/<state>` mode (`vote_ics()` in `index.php`). All-day events with a reminder at
  9 a.m. the day before: the last day of early voting (43 states), or the mail deadline with the
  verified return text (CA, CO, HI, OR, WA), and Election Day with the polling-place lookup link (left
  out where the mail deadline is Election Day). No poll hours in the files, since they vary. An early
  event drops the day after its date (Texas has only Election Day in the Oct 31 preview); the Nov 4
  build writes no files and no button. Plain link, no `download` attribute, so iPhone opens its Add
  to Calendar sheet; `_headers` serves `text/calendar` with `X-Robots-Tag: noindex`. The build checks
  each file (CRLF, VCALENDAR wrapper, lines of 75 octets or fewer; mutation-tested) and stops on PHP
  warnings in it. Facts, Spanish, SEO and event QA pass; stale scan unchanged at 27. Every state page
  gets today's lastmod, which is true (the button is new on each).
- **Oct 26, item 2 (Early voting ends).** State heroes with verified early voting still ahead show
  "Early voting ends: Friday, Oct. 30" with the same gold count pill as the Election Day line under it
  ("La votación anticipada termina: lunes 2 de noviembre", "faltan 7 días"). `vote_early_countdown()`
  in `index.php`; the footer countdown script gained `data-hide-after`, which hides the whole line once
  the visitor's date passes the end, before the next build drops it. Checked with a faked browser
  clock: Oct 30 says Today, Oct 31 hides it. Previews: Texas loses the line on Oct 31, Vermont keeps
  it through Nov 2, none on Nov 4. Facts, Spanish and SEO QA pass; stale scan unchanged at 27.
- **Oct 26, item 3 (profile-picture size).** Under the maker preview, the same image at 48 and 32 px
  in circles ("How it looks as a profile picture" / "Así se ve como foto de perfil"), refreshed with
  every redraw from the same blob, hidden in Story mode and from screen readers (a visual duplicate).
  No page errors in a browser check.
- **Oct 26, item 4 (Copy image description).** A "Copy image description" button beside Open full
  image copies the sticker's colors, state and words as they are drawn: "Sticker in plum and cream
  for Texas reading VOTE EARLY, OCT 19–30." (state palette: "Sticker in Texas state colors reading
  ..."; Spanish: "Sticker en ciruela y crema para Washington que dice: VOTA POR CORREO, HASTA EL 3
  NOV."). Checked in a browser after a color change. Found and fixed on the way: the Spanish maker
  named the state palette in English ("Texas colors"); it now says "Colores de Texas". Facts,
  Spanish, SEO and event QA pass.
