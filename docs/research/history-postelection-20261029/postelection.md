# After Election Day: Certification Dates, Ballot Status, Results Pages (Oct 29, 2026)

Standard mode (Deep Research monthly limit reached). Finished in 2m 26s, 694 sources.
Thread: https://www.perplexity.ai/search/79161382-5284-4d2d-b0c1-5cdd394b0cbe
Prompt: `postelection-prompt.txt`. **Leads only; none of the state dates below were read by us on the official page.**

## State Certification Dates (Perplexity's table, condensed; quotes as it gave them)

| State | Date (2026) and basis | Official wording it quoted |
|---|---|---|
| AL | Nov 25, calendar (state canvass) | "...canvass election returns ... within 22 days after the election." |
| AK | Nov 25, calendar target | "Target date for State Review Board to certify election." |
| AZ | Nov 23, calendar | "Official Statewide Canvass of the November 3, 2026 General Election" |
| AR | Nov 18 is county-to-state; no state date verified | "Deadline for the CBEC to certify election results to the Secretary of State." |
| CA | Dec 11, calendar (county Dec 3) | "Statement of Vote certified by the Secretary of State" |
| CO | not verified | |
| CT | Nov 25 by statute (last Wednesday of the month), 2023 compilation | §9-315 |
| DE | Nov 5, 10 a.m., Superior Court canvass starts (statute) | 15 Del. C. §5701 |
| DC | Dec 2, tentative (DCBOE voter guide) | "...tentatively scheduled for Wednesday, December 2, 2026." |
| FL | Nov 17, statute (14th day) | §102.111 |
| GA | not verified | |
| HI | after the contest period (statute) | Act 47 of 2022, §11-155 |
| ID | by Nov 24, statute (meet within 21 days) | Idaho Code §34-1211 |
| IL | by Dec 4, statute (within 31 days) | 10 ILCS 5/22-7 |
| IN | not verified | |
| IA | by Nov 30, calendar | "State Canvass of Votes Must Be Complete" |
| KS | by Dec 1, calendar | "Last day for State Board of Canvassers to certify official results." |
| KY | by Nov 23, statute (third Monday) | KRS 118.425(5) |
| LA | by Nov 17 absent a contest, statute | R.S. 18:574 |
| ME | by Nov 23, statute (20 days) | 21-A M.R.S. §722 |
| MD | Dec 3 COB, calendar (board convenes) | "Deadline for the State Board of Canvassers to convene to certify election results." |
| MA | not verified; no unofficial results published by the state | |
| MI | by Nov 23, calendar and statute | MCL 168.842 |
| MN | Nov 19, official results page | "...until the Minnesota State Canvassing Board meets on Thursday, November 19, 2026 to certify results." |
| MS | Dec 3, calendar | "Declaration of General/Special Election Results by Secretary of State." |
| MO | by Dec 8, statute (second Tuesday in December) | RSMo 115.511(2) |
| MT | Nov 30, calendar (scheduled) | "State canvass completed ..." |
| NE | Nov 30, calendar | "Board of State Canvassers meets to certify the election results." |
| NV | Nov 24 (fourth Tuesday), not fully verified | |
| NH | not verified (after recount deadline) | |
| NJ | by Dec 3, calendar | "Deadline for Meeting of Board of State Canvassers to Certify General Election Results" |
| NM | not verified for 2026 | |
| NY | on or before Dec 15, statute | Election Law §9-216(2) |
| NC | Nov 24, 11 a.m., official guide | "The date of the state canvass for the November general election is Tuesday, November 24, 2026, at 11:00 a.m." |
| ND | Nov 18, 10 a.m. CST, media guide (county date conflict) | |
| OH | not verified (Nov 24 is county) | |
| OK | Nov 10, 5 p.m., filing packet | "Certification of Final Results" |
| OR | Dec 10, voter pamphlet | "The deadline for the results to be official and for the election to be certified is December 10, 2026." |
| PA | not verified (Nov 23 is county) | |
| RI | after recounts and hearings | |
| SC | within 10 days (Nov 13) | "Certification: No more than 10 days following Election Day" |
| SD | by Nov 10, calendar | SDCL 12-20-47 |
| TN | not verified | |
| TX | Nov 21 first possible state canvass day | |
| UT | Nov 23, calendar | |
| VT | Nov 10, 10 a.m. canvass starts (statute) | 17 V.S.A. §2592(g) |
| VA | Dec 7, official announcement | "Dec. 7 – State Board of Elections certifies results." |
| WA | by Dec 3, WAC 434-262-120 | |
| WV | Dec 3 is county-to-state | |
| WI | Dec 1 per a legislative explainer, not verified | |
| WY | Nov 10, calendar | "State Canvassing Board meets to certify state abstract." |

## Ballot Status and Results Pages

Perplexity listed official ballot-status and results pages for all 51 (full list in the thread). Points worth
keeping: trackers mean different things ("received" is not "accepted"; New Jersey defines "Accepted" as "Certifies
that your ballot has been counted"; Washington separates Received from Accepted); Massachusetts says "We do not
publish unofficial election results"; Wisconsin and New Jersey post no statewide election-night results (counties
do); Michigan warns ballots returned at early voting or on Election Day may not show as "received" online.

## What We Verified and Used

- **EAC, "Election Results, Canvass, and Certification"** (https://www.eac.gov/election-officials/election-results-canvass-and-certification,
  dated July 30, 2026), read with curl: "However, even when those results say "100% Precincts Reporting," they are
  still unofficial." and "The election results reported on election night are never the final, certified
  results." The page opens: "Have you ever wondered what happens to your ballot after you get your I Voted
  Sticker?" Used for one post-election home FAQ ("When are the election results official?"), EN and ES, shown
  only from Nov 4 (`'post' === vote_phase()`).

## Not Built (proposal in the loop log)

A per-state "Results become official" line in post-election mode: a `dates.certify` fact per state (date, quote,
official URL) shown under "Voted? Get Your Sticker." Each of the 51 dates needs a read on the official calendar or
statute; about 15 are not verified even by Perplexity, and several of its dates are meeting dates or county
deadlines, not completion dates. Too much unverified surface for Nov 4. The state pages already keep the verified
ballot tracker (`mail.track_url`) and the official election office link after Election Day.
