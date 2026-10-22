# Vote Sticker Backlog

Open items, newest first. Started October 21, 2026. Move an item to `docs/history.md` when it ships.

## Needed Before Election Day

| Item | Why | Notes |
|---|---|---|
| **Open fact calls** | All 51 guides live October 21. These stayed off the pages because official sources disagree or couldn't be read. | SC early voting close (5 vs 6 p.m.; statute says 6), NJ cure deadline, MI photo rule, KS mail return (SOS page contradicts itself), AR in-person absentee deadlines, MT late-registration cutoff (court blocked SB 490; live answer now says ask your county), WY regular registration date (Oct 19 PDF vs. 14-day rule), WV provisional-without-ID path, VT mail deadline (statute vs. SOS page), NH thin (sos.nh.gov blocks reading), Vermont/Maine/Montana/ND/SD early windows not set (no single official date). Alaska and Maine could get a ranked-choice link to Resist Now's Learn guide. |
| **History of the I Voted sticker** | A sourced history page at `/history/`, linked from the state sticker sections, the home FAQ and About. | Plan: `docs/sticker-history-plan.md`. Waiting on Garrett's research and data; data visualization after. |
| **"Early voting has ended" family** | Texas ends October 30, others sooner. A page that still says "early voting runs" after it closes is wrong. | Plan Section 5.1 (`early_over`). Depends on the daily rebuild. |

## Done October 21

- Daily rebuild: `.github/workflows/daily-build.yml`, 11:05 UTC, first run passed every step. Secrets set from the environment.
- ivotedsticker.org: A records to 192.0.2.1 (proxied) and one dynamic redirect rule, all paths 301 to votesticker.org. HTTPS verified.
- Per-state mail ribbons (CA, CO, HI, OR, WA) and sticker date lines; per-image alt text for all 369 ready-mades (`site/assets/states/alts.json`).

## Content and Copy

- ~~Mail-state ribbon with real dates~~ (done).
- **Old mail-ribbon note.** Washington's ribbon reads "VOTE BY MAIL, DUE NOV 3"; the review suggested "DROP BOX BY 8 P.M. NOV 3". The ribbon comes from `states.js` for every mail state, and return rules differ (postmark vs. received), so this needs a per-state ribbon field, not a blanket change.
- ~~Per-image alt text~~ (done). Old note: ("Plum circle sticker reading VOTE EARLY, Texas, Oct. 19–30"). Needs the printed text per image; the Codex prompt files list it for the Codex sets.
- **Mail-state default date line.** Washington's maker starts on "DUE NOV 3"; "BY 8 P.M. NOV 3" fits better. Same per-state field as the ribbon.
- **"Signed, sealed, delivered" headline:** declined. Three lines over the 10-character limit, and it's a song title. "MY BALLOT / IS IN" shipped instead.

## Tool Ideas (Brand Brief)

- A "Done" line after switching to I voted: "Send this to someone who hasn't voted yet." Share link only, no tracking.
- An add-to-calendar file for your voting day, built in the browser. Nothing stored.
- "Early voting ends in N days," shown only when the verified dates make it true (needs the daily rebuild).

## Maker Ideas From the Sticker History Research (October 22)

Already in the maker: square and story sizes, native share on phones, a free-text date line, I voted and
I'm voting messages, Spanish. Worth adding, in rough order of value before November 3:

- **"First time voter" message** (FIRST TIME / VOTER, and PRIMERA VEZ / VOTÉ or similar after Spanish review). The research's strongest new message; fits the 10-character line rule.
- **See it at profile-picture size.** A small 48 px circle preview next to the big one, so people can check it reads where friends will see it.
- **Copy alt text.** A button that copies a plain description of the sticker ("Plum sticker reading VOTE EARLY, Texas, Oct. 19–30") for people who add alt text to posts.
- **Cut-out sticker (transparent PNG)** for laying over a photo. Disc designs only; square designs have no edge to cut.
- **Portrait 1080 x 1350** for Instagram feed posts, beside square and story.
- **"Future voter"** for kids and families. Low priority; nice after the election too.

Not doing: counters, event tracking, stickers-made maps, uploads or AR filters. They add tracking or
moderation, and the brand promise is that nothing you make leaves your device.

## After the Election

- **Privacy and accessibility statements.** A footer review suggested Privacy, Terms and Accessibility links. Today the About page covers privacy in one line ("Your sticker is made on your device and is never uploaded"). After November 3, consider one short "Privacy and Accessibility" section on About rather than new thin pages.
- **"Submit a sticker."** Suggested for the history collection; needs a rights and moderation plan first.

## Design and Type (Review of October 21)

The review recommended Fraunces + Public Sans and warned against common AI-template fonts. The site
uses none of those: Anton (display, matching the stickers) and the device's system font for body text.
Switching the display face to a serif would split the site from the stickers, so it stays. The cheap,
evidence-backed parts are worth doing:

- `font-variant-numeric: tabular-nums` on the dates table and state status lines.
- Body text in the guide sections at 17px, line-height about 1.6.
- Check the muted gray (`--muted`) used for source lines and status labels against WCAG AA on cream.
- Confirm every tap target is at least 44 px (the card QA already checks buttons; swatches and chips were sized to 44 and 40).
