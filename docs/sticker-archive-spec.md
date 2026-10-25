# Sticker Collection and 2026 Archive Spec

Status: backlog, not approved. Written October 25, 2026 from Garrett's Perplexity rounds on the
official-design gallery (Phase 3 of `docs/content-plan-20261023.md`) and the post-election archive.
Build after November 3, except the gallery data, which can be collected now.

## What It Is

One collection of voting stickers with their stories: who made each one, which office issued it, how
it was chosen, where voters got it, and what we can show. It replaces two ideas that overlapped (a
Phase 3 gallery page and a separate election archive) with one page that starts as the 2026
collection.

It is about stickers. Election context is the date, the election's name and links to the official
ballot and certified results. No candidate lists, campaign summaries or results tables.

## URLs

| URL | What |
|---|---|
| `/stickers/` | The collection, filterable by state, message and origin (Spanish `/es/stickers/` after review) |
| `/stickers/2026/` | Only if a second year exists; until then `/stickers/` is the 2026 collection |
| `/texas/` and the other state pages | Stay the evergreen state pages; never redirected |

State filters are page controls, not new URLs. A state gets its own collection page only when it has
enough documented designs and reporting to stand alone (Michigan's nine 2026 winners might).

The October 23 decision named `/official-stickers/` as the working URL. `/stickers/` holds both our
designs and official ones, so it replaces that name.

## Two Kinds of Card, Visibly Different

**VoteSticker designs** (ours): image, "Download PNG", "Make your own". Dated reminders ("Vote early
Oct 19–30") carry a label beside the image: "Past-election reminder · November 2026. The dates are
historical." The artwork is never stamped or altered. "Make your own" opens the current maker, not the
old dates.

**Election office designs**: issuing office, designer (adults by name; minors only where the official
release names them, and we still prefer first name and school), year and which elections, how it was
chosen, where voters got it, link to the announcement. Image only when permission or a license is
recorded; otherwise a short description and "View the official announcement".

## Record Fields

Kept in a data file beside the facts files, checked like them (official source, quote, checked date):

- id, state, county (or null for statewide), origin (`votesticker` or `office`)
- title, alt, image path (ours) or null
- message type: already voted, planning, deadline reminder
- year, elections used for (November general, June primary, several, not stated)
- placement: active gallery, collection, or both
- designer, issuing office, selection process
- distribution: early voting, Election Day, mail packet, office counter, other pickup, each with its
  own quote and election
- image permission: confirmed (with the grant), license (with the license line), or none
- photo license kept apart from design permission (a CC photo of a sticker doesn't license the design)
- source URL, quote, source date, checked date

## Rules

- A contest winner is not proof of statewide distribution (West Virginia's 2026 winner went to Raleigh
  County for the primary; Kentucky's goes to Laurel County and any clerk who asks).
- A sticker mentioned in mail instructions is not proof it is in the packet (Clark County, NV).
- "Published" or "available to download" on an official site is not permission for us to reproduce it.
- Distribution is dated: "For the November 2026 election, Snohomish County offered stickers at ..."
  Never "Pick one up at ...".
- A design used in several years is one record with several uses, not a copy per year.

## Starting Data (Perplexity, October 25, Unverified Until Read)

Thirteen state records (several hold more than one design, some only partial evidence) to verify on the official pages before they go in. Permission: none found for any of them.

| State | Design | Who and how | Announcement |
|---|---|---|---|
| Michigan | Nine winners (three categories) | 2,095 entries, public vote of 53,000+; clerks order them for November | michigan.gov/sos, Aug 18, 2026 |
| North Dakota | "You Betcha I Voted" | Haylee Senger, Bottineau County deputy auditor; 478 entries, public vote | sos.nd.gov, Mar 5, 2026 |
| Kentucky | Sun, pasture, Kentucky outline | Student, Laurel County; State Fair vote | kentucky.gov, Sep 3, 2026 |
| New Hampshire | Six student winners | Panel of local election officials | sos.nh.gov PDF, Nov 13, 2025 |
| Alaska | 2026 pamphlet cover and stickers | Featured artist Amanda Rose Warren | elections.alaska.gov, Sep 10, 2026 |
| Alabama | America 250 | Not named | sos.alabama.gov |
| Ohio | America 250, limited edition | Not named; all 88 county boards | ohiosos.gov, Jun 12, 2026 (already on the Ohio page) |
| Montana | Outline and mountains, red, white and blue | Not named | sosmt.gov, Feb 26, 2026 |
| West Virginia | First statewide contest winner | Student, Raleigh County; 15,947 votes | sos.wv.gov, Mar 11, 2026 |
| Louisiana | Artist series, sixth sticker | Terrance Osborne, commissioned | sos.la.gov, Jul 28, 2026 |
| Georgia | Exclusive design | Steve Penley | sos.ga.gov, Sep 29, 2026 (already on the Georgia page) |
| Colorado | Digital sticker | High school contest winner | sos.state.co.us, Oct 12, 2026 |
| Vermont | Student contest | K-8 students; this year's winners from Burlington | sos.vermont.gov, Oct 23, 2026 (on the Vermont page) |

Wikimedia Commons had two freely licensed 2024 to 2026 photos (an early voting sticker, CC BY 2.0; a
Cass County, MN election judge with sticker rolls, CC BY 4.0). They license the photos, not the
designs, and neither names the issuing office. Not enough to build on.

## Election Context

The test for any fact: without it, would the reader understand the sticker or its history less well?
If not, it belongs in an election guide, not here.

**Include when documented:** the specific election (primary, general, special, local), the issuing
office, who could get the sticker (early, Election Day, mail voters), the occasion or theme (America
250), how it was chosen, a change in distribution (first year in mail packets, new pickup sites),
languages and accessibility versions, and its relation to other editions (new design, reprint, reused
across elections).

**Numbers only when they answer a sticker question:** designs submitted, contest votes, stickers
printed or ordered, packets meant to include one, locations offering it. Each keeps its own meaning:
"printed 100,000" is not "100,000 voters received one". Turnout is optional background and never
presented as caused by a sticker.

**Context box, same on every collection:** election name, date, issuing office, sticker use, links to
official election information and certified results. One extra sentence only when it earns its place
("Used for both the primary and the general election, not made for November").

**Leave out:** candidates, platforms, endorsements, predictions, results tables, campaign controversy,
political analysis, generic state description, and any claim about voter enthusiasm from photos or
downloads. The one exception: a documented design change because it looked like campaign material,
told as a design decision.

**Length:** a card gets one or two sentences; a state or year collection gets the context box and one
paragraph on what makes it distinctive; a feature story gets more only with original reporting. No
word counts.

## Later, If It Proves Useful

- "Meet the designer": five questions (first sketch, the local detail, what was hard at sticker size,
  how it was chosen, first time seeing someone wear it). Needs Garrett's OK for outreach.
- Election office questions: will voters get a sticker at early voting, in person absentee, by mail;
  where are extras; may we show the design with credit. Outreach is Garrett's call.
- Collections once there are enough verified examples: student designs, mail ballot stickers, state
  birds and landmarks, America 250.
- Sketch-to-sticker comparisons, where permission allows.
- Participation snapshot (official turnout, by method) only where the office reports it, with its basis.

## Rollover After November 3

1. Expired reminders leave the active gallery and maker defaults; their image URLs stay.
2. The collection page lists them with the past-election label.
3. State pages get a "From past elections" link to `/stickers/` filtered to the state.
4. Review labels, credits and permissions before publishing; the build assembles, a person approves.
