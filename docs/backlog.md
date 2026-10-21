# Vote Sticker Backlog

Open items, newest first. Started October 21, 2026. Move an item to `docs/history.md` when it ships.

## Needed Before Election Day

| Item | Why | Notes |
|---|---|---|
| **Daily rebuild and deploy** | Three things only change at build time: "Early voting is open today in N states" on the home page, facts with an `until` date (Washington registration switches to past tense October 27), and any ribbon or lead that should flip when a state's early voting ends. | A launchd job or a scheduled run of `build_static.py` + `wrangler pages deploy` + QA at 6 a.m. Central from October 23. See `docs/content-strategy-state-pages.md`, Phase 3. |
| **ivotedsticker.org redirect** | Garrett bought it October 21. | Waiting on the Cloudflare plugin sign-in (`/mcp`). Plan, for his OK: in the ivotedsticker.org zone only, proxied placeholder records `@` and `www` (AAAA `100::`) and one redirect rule, every path on both hosts 301 to `https://votesticker.org/`. Nothing changes on votesticker.org. |
| **Facts files for the other 49 states** | Texas and Washington are done and reviewed. | Per-state prompt in the content plan, Section 8.2; brand brief in `docs/brand-brief.md`. Tier 1 order in Section 8.4. |
| **"Early voting has ended" family** | Texas ends October 30, others sooner. A page that still says "early voting runs" after it closes is wrong. | Plan Section 5.1 (`early_over`). Depends on the daily rebuild. |

## Content and Copy

- **Mail-state ribbon with real dates.** Washington's ribbon reads "VOTE BY MAIL, DUE NOV 3"; the review suggested "DROP BOX BY 8 P.M. NOV 3". The ribbon comes from `states.js` for every mail state, and return rules differ (postmark vs. received), so this needs a per-state ribbon field, not a blanket change.
- **Per-image alt text for the ready-made sets** ("Plum circle sticker reading VOTE EARLY, Texas, Oct. 19–30"). Needs the printed text per image; the Codex prompt files list it for the Codex sets.
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
