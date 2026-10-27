# Vote Sticker: Resume Here (October 27, 2026, Afternoon)

Election Day is Tuesday, November 3. Supersedes `NEXT-SESSION-20261022.md`. Two pushes went out
October 27 (6:32 a.m. and 12:48 p.m.); both deployed, live QA passed, IndexNow 200. One commit is
local and unpushed: `8bba70a` (analytics doc). It goes out with the next push or the daily build.

## Open Work, in Order

1. **October 28, about 6:30 a.m.:** confirm the daily build ran (dispatch `gh workflow run
   daily-build.yml --ref main` if it hasn't started; the cron often runs hours late). Then Louisiana and
   Pennsylvania should show early voting ended (title, gallery, share image). Push `8bba70a` with it.
   Log the check in `docs/LOOP-election-prep.md`.
2. **October 28:** the first GA4 BigQuery table should arrive as `analytics_559738937.events_YYYYMMDD`
   in project `seo-command-ctr`. Check it exists, and check the dataset's default table expiration.
   Every dataset in that project defaults to 60 days, the BigQuery sandbox sign. **Ask Garrett whether
   billing is linked on `seo-command-ctr`** (he hadn't answered). If not, each daily table deletes itself
   after 60 days.
3. **October 29:** Search Console look (election prep loop). First read of the new GA4 fields:
   `sticker_builder_start` count against maker saves, and cancels followed by a retry (`prev_result`
   `aborted`).
4. **The dated loop items:** Nov 1 `archive_search --index`; Nov 2 banner and the Kansas re-read; Nov 4
   post-election mode and the title check (visibility item 9); Nov 5 remove the cron, log the release,
   switch the image-filename 301s (item 11).
5. **The visibility loop** (`docs/LOOP-visibility.md`): the Monday cycle, the history page after Nov 3,
   watching the Spanish home page. Measure the Oct 27 heading change ("I Voted Stickers by State") from
   its recrawl, 14 days, same days of the week.

## What Shipped October 27

- **Maker grouped:** preview, message, a sideways layout row on phones, colors, Save. Under More options:
  the date line, size, sunburst, one Tilt checkbox (tilt right dropped), crop preview, "Cut out the
  circle" with a plain explanation, and Copy image description. A sticky 108 px live thumbnail inside
  More options on phones only. One 48 px profile preview ("How it appears as a profile picture").
- **Measurement v2** (`docs/analytics.md`): `sticker_builder_start`, `sticker_hold_open` (press and hold
  is no longer a save), `attempt` and `prev_result`, guards for a double click and a tap while the share
  sheet is open. The three new parameters are registered in GA4 as custom dimensions. Report these as
  save handoffs, never confirmed saves to Photos.
- **Fixes on every state page:** "Election Day hours:" (it sat under "Vote Early" as if it were early
  voting hours); the vote-by-mail summary and preview alt text (CA, CO, HI, OR, WA read "ID: Official
  info"); Title Case info headings; FAQ save wording; spacing found by a gap audit at 390, 820 and 1024
  px; a hero jump link to the voting facts.
- **Outside UX reads:** what was deferred and where we pushed back is in `docs/backlog.md`, "Outside UX
  Reads, Deferred (October 27)".

## Access Changes Garrett Made October 27

- Google Analytics Admin API enabled on Cloud project `seo-command-ctr`.
- The service account `garrett@seo-command-ctr.iam.gserviceaccount.com` has **Editor** on the Garrett
  Digital GA4 account. It can now register custom dimensions, mark key events and set retention.
  Garrett may set it back to Viewer.
- GA4 property 559738937 is linked to BigQuery: Daily export only (no streaming, no user data), US.
  No other property under Garrett Digital is linked yet. Client properties sit in the clients' own
  accounts, so ask the client before linking those.
- Still on Garrett's list: mark `sticker_save` as a key event and set retention to 14 months (the
  service account can now do both if he says so), and the Search Console bulk export.

## Elsewhere

- Garrett Digital backlog (`astro-sites/garrettdigital/docs/backlog.md`): GD-324 to GD-326, client
  onboarding guides for the Search Console bulk export, scheduled Search Console exports, and linking
  GA4 to BigQuery. GD-326 lists Garrett's walkthrough video as source material. Committed there, not
  pushed.

## Not Ours to Commit

Untracked in this repo and owned by other sessions: `data/`, `docs/early-sticker-research-2026-10-23.md`,
`docs/early-voting-research-2026-10-23.md`, `docs/sticker-roundup-competitive-research-2026-10-23.md`.
Leave them alone.
