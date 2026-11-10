# Guides Plan

Set November 9, 2026. The first guides under `/voting/`, with their URLs, search terms and outlines. Standards are in
`docs/editorial-standards.md`. Nothing here is built yet.

## Why Guides

Between elections, the state pages have little new to say. Guides that explain how voting works keep the site useful all year,
and each one points readers back to their state page and their sticker.

## URLs and Search Terms (Ahrefs, US, Nov 9)

Volumes are monthly averages that include the 2026 election season, so expect less in 2027. KD is keyword difficulty, 0 to 100.

| Order | URL | Main terms (volume, KD) | Notes |
|---|---|---|---|
| 1 | `/voting/runoff-elections/` | what is a runoff election (2,100, 2); runoff election (1,700, 5); when does a runoff election occur (1,300, 1) | Easiest to rank. A small state dataset makes it the pilot for the guide page type |
| 2 | `/voting/primary-elections/` | primary election (22,000, 27); primaries (18,000, 35); what is a primary (800, 14; traffic potential 12,000) | What a primary is and the types in brief; links to guide 3 for the state rules |
| 3 | `/voting/open-and-closed-primaries/` | open primary (4,200, 35); closed primary (3,700, 9); open vs closed primary (1,000, 24); can independents vote in primaries (900, 21); open primary states (700, 46); jungle primary (700, 24); closed primary states (250, 16) | The state data page. A separate parent topic from "primary election", so it's a separate page |
| 4 | `/voting/special-elections/` | special election (8,100, 12; mostly news); what is a special election (700, 8) | Special elections happen every year, including off years |
| 5 | `/voting/midterm-elections/` | what are midterm elections (13,000, 27); what is a midterm election (3,300, 11) | Evergreen, though demand peaks every four years |
| Later | `/voting/general-elections/`, `/voting/provisional-ballots/`, `/voting/election-certification/` | general election (3,300, 34); provisional ballot (800, 24); certification terms near 0 | Certification goes last on demand; recheck before building |
| Later | `/voting/ranked-choice-voting/` | ranked choice voting (18,000, 56) | Highest demand, but advocacy groups dominate it. Wait until the neutrality rules have been tested |

Spanish searches for these terms are small (for example "elecciones primarias" 30 a month). Spanish editions serve readers
of the Spanish site, not search volume.

## What the Competition Leaves Open

Read or seen Nov 9:
- **"closed primary":** the top result is an archived page that its own publisher says may be out of date. It has no date,
  no sources and no state answers.
- **"can independents vote in primaries":** the top two results come from groups that advocate for independent voters.
  Ballotpedia's and NCSL's state lists rank sixth and seventh.
- **EAC's "Primary Election Types"** is dated and official, gives national shares, and links a state spreadsheet. It doesn't
  answer by state on the page.
- **"special election"** is mostly news results. CRS ranks second with a short explainer.

Ballotpedia's and NCSL's pages haven't been read closely yet.

**Our edge:** a neutral answer for your state on the page, with its source and checked date. Then a labeled map, a visible table
and plain language, in English and Spanish, from a site people already use for their state's dates.

## Outlines

### Runoff Elections

- **Answer:** a runoff is a second election between the top candidates when no one reaches the vote share a state requires to win.
- **Key facts** (counts from data): states with primary runoffs, states with general election runoffs, the usual threshold.
- **How it works:** a decision flow ("Did anyone pass the threshold? Yes: winner. No: the top two meet again"), with made-up numbers.
- **Primary runoff vs. general runoff vs. top-two vs. ranked choice:** a comparison table showing what each one is and when it
  happens. Mechanics only.
- **In your state:** does the state have runoffs, for which elections, the threshold, and how long after the first election.
  Source and checked date for each.
- **Questions people ask:** When does a runoff happen? Can I vote in a runoff if I skipped the first round? Can I switch parties
  for a primary runoff?
- **Doesn't cover:** local rules that differ from state rules, and special elections.

### Primary Elections

- **Answer:** a primary is how parties pick their candidates for the general election, and in some states how the field is
  narrowed for all voters.
- **Key facts:** the main types, presidential primaries vs. caucuses, and that rules vary by state.
- **How it works:** the timeline from primary to possible runoff to general, illustrative only.
- **Types in brief:** one paragraph each, linking to the open and closed primaries guide.
- **Doesn't cover:** party caucus and delegate rules in detail.

### Open and Closed Primaries

- **Answer:** in a closed primary only a party's registered voters can vote in its primary; in an open primary any voter can
  choose one party's ballot. Many states fall in between.
- **Who can vote in which primary:** a matrix with voter status (registered with the party, registered with another party,
  unaffiliated) against primary type (open, closed, partially closed, top-two).
- **In your state:** the primary type, whether unaffiliated voters can vote, and whether and by when you can change party.
  Separate rows for presidential primaries where they differ.
- **Map and full table:** published only when all 51 rows are checked.
- **Questions people ask:** Can independents vote in primaries? Can I vote in both primaries? What's a jungle primary?
- **Doesn't cover:** arguments for and against each system, beyond one attributed section if needed.

## Build Order

| When | What |
|---|---|
| Dec 2026 | Between-elections mode, the 2026 sticker page, next-election links (already planned) |
| Feb to Mar 2027 | Guide page type, the "how we check facts" section on About, runoff data and the runoff guide as the pilot |
| Mar to Jun 2027 | Primary type data for all 51 states, read on official sources; the primary elections and open and closed primaries guides |
| Summer 2027 | Primaries guides live and reviewed, ahead of the 2028 primaries; the `/voting/` hub and menu item once three guides exist |
| Late 2027 | Special elections, midterm elections |

## Build Needs

- **A guide page type fed by data files,** with fields for byline, dates, review-due, change notes and sources.
- **New per-state fields,** each with source, checked date and expiry: `primary.type`, `primary.unaffiliated`, `primary.switch_by`,
  `runoff.primary`, `runoff.general`, `runoff.threshold`.
- **Components:** tile map with labels, comparison matrix, step flow, timeline, count line, state picker.
- **QA:** review-due, dates not taken from the build, a source on every state row, counts that match the data, EN and ES parity,
  and widths.
- **Article schema** from the same fields as the visible dates.
