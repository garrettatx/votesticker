# Vote Sticker Backlog

Open items, newest first. Started October 21, 2026. Move an item to `docs/history.md` when it ships.

## Needed Before Election Day

| Item | Why | Notes |
|---|---|---|
| **Facts files for the remaining 39 states** | 12 live (TX, WA, GA, VA, FL, NC, MD, PA, NV, NJ, NY, MI). Batches C (OH, TN, IN, SC, WI) and D (IL, CA, MA, AZ, CO) running. | Agents write files without `"live"`; review, then mark live. Open calls: NJ cure deadline (Nov 14 timeline vs. 48 hours before certification), MI photo rule (two SOS pages disagree), GA selfie FAQ could cite the stronger SOS "No." |
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

## Design and Type (Review of October 21)

The review recommended Fraunces + Public Sans and warned against common AI-template fonts. The site
uses none of those: Anton (display, matching the stickers) and the device's system font for body text.
Switching the display face to a serif would split the site from the stickers, so it stays. The cheap,
evidence-backed parts are worth doing:

- `font-variant-numeric: tabular-nums` on the dates table and state status lines.
- Body text in the guide sections at 17px, line-height about 1.6.
- Check the muted gray (`--muted`) used for source lines and status labels against WCAG AA on cream.
- Confirm every tap target is at least 44 px (the card QA already checks buttons; swatches and chips were sized to 44 and 40).
