# Election Lifecycle Plan, October 25, 2026

How the state pages, home page and maker change from now through the weeks after November 3. It takes
the parts of the October 25 Perplexity suggestions that fit the site's goal (help someone vote, get a
sticker, share it with a friend) and that can ship before Election Day on the static build.

## The Rule

Each action ends at its own deadline and gives way to the next useful one. The sticker gallery, the
maker and "I voted" designs never go away. An official link stays on the page after its invitation is
retired ("Find where to vote" becomes "Official election information").

The site never claims a live status it can't know. It states the schedule ("Polls are open 7 a.m. to
7 p.m. local time") rather than "polls are open now", and it never guesses the visitor's location from
the browser or IP.

## Modes

| Mode | When (daily build, 6:05 a.m. Central) | State page leads with | Maker default |
|---|---|---|---|
| Early voting open | Today, until each state's end date | Dates, where to vote early | Vote early |
| Early voting ended | Day after the state's last early day (built Oct 23) | Election Day plan, polling place lookup | Vote Nov 3 |
| Election Day | November 3 build | Banner: Election Day, verified poll hours or "check your polling place and hours", polling place lookup; mail states lead with the verified return rule | Vote today / I voted |
| Voted | November 4 build | "Voted? Get your sticker." Gallery first; ballot tracking link where the state has one | I voted |
| Between elections | After follow-up deadlines (about December 1) | Evergreen intro, gallery, official upcoming-elections link, past dates collapsed | I voted |

Election Day and Voted are new. Between elections is after the election and not urgent.

## Election Day Banner

One component, in the page flow above the state heading, not sticky, no countdown, no red, no "last
chance". `<section>` with a real heading.

- Eyebrow: NOVEMBER 3 · ELECTION DAY.
- Heading: "Voting today? Find your polling place." (in-person states) or "Still have your ballot?
  Check how to return it today." (mail states).
- One line of verified detail: the poll hours in local time, or the return rule as the facts file
  words it (received by vs. postmarked by are different, never merged).
- Primary link: the state's official polling place or drop box lookup (`where.locator_url`).
- Secondary link: "Already voted? Get your sticker" to the gallery.
- Where hours vary by county or the state spans time zones, the line says "Hours can differ by
  location; check yours before you go."
- Home page: "Voting today? Start with your state." pointing at the existing state picker.

## Voted Mode (November 4)

- Intro: "Voted? Get your sticker." Gallery right after it.
- Post text: no "there's still time to vote" lines; the already-voted versions stay.
- Ballot tracking link only where the facts file has a verified tracker.
- Dates table moves below the gallery under "November 3, 2026 election (past dates)".
- Titles drop the early voting dates: "[State] I Voted Stickers".

## What We Are Not Doing Before November 3

- A per-action deadline data model, mixed-closing banners, or "polls close soon" switching during the
  day. The static build can't time these safely; the schedule line is true all day and the November 4
  build retires it.
- Inferring a "next election" for each state.
- Separate archive pages per state. A collapsed past-dates section is enough.
- New pages per sticker variation.

## After the Election

- Official-design gallery (Phase 3): describe and link official designs; no images until the office's
  permission or a license is recorded. Designer, office, year, how it was chosen, where voters got it.
- Physical sticker answers stay, dated ("For the November 2026 election, ..."), with current
  availability reset to "not yet confirmed" for the next election.
- Designer interviews and election office questions (outreach is Garrett's call).
- Descriptive image filenames, only with a redirect from the old URLs.

## Data Needed

- **Election Day poll hours** for each state, in local time, with a note where counties set hours or the
  state spans time zones. Source: the state's official page. Loop batch 3, October 25.
- **Ballot tracker link** for each state that has one. Same batch.

## Done October 25

- Image sitemap: every page lists the sticker images it shows (393 images on 108 pages). Google already
  shows the stickers in image search (125 impressions October 20 to 25, positions mostly 30 to 100,
  no clicks); the sitemap helps discovery of the rest.
