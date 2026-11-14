# Research: Open and Closed Primaries (Batch 1 of 3)

Pass 6 of the evergreen-guides loop. Batch 1 covers 17 jurisdictions: Alabama through Kansas, with the District of Columbia.
Batch 2 is Kentucky to North Dakota; batch 3 is the rest. The data file is `docs/research/primaries-states-batch1.json`.

## Method

All sources were read on 2026-11-14 in Chrome (Claude in Chrome), with WebFetch, or from official PDFs fetched with WebFetch
and converted to text locally. No Firecrawl. Each row rests on a statute on the legislature's own site (or the state's
official code compilation) and, where one was found, the election office's plain-language page. Perplexity's answer was used
only as a list of leads. NCSL's "State Primary Election Types" page and the EAC primary types spreadsheet were **not opened
in this pass**, so no NCSL or EAC errors are listed; batch 2 or 3 should open them once to compare. Quotes are under 30 words.

Sites that blocked WebFetch (403) or failed and were read in Chrome instead: California leginfo, Arizona SOS, Florida DOS,
Georgia SOS, Connecticut (certificate error), Alaska statutes, Illinois ILGA (certificate error).

### Perplexity Prompt

One prompt, "VoteSticker.org Research" project, **Search** mode (switched from Computer before sending):
https://www.perplexity.ai/session/b638f704-19a3-4d3f-995b-719d64815da8

> Context: VoteSticker.org is a neutral, nonpartisan, bilingual site that explains how voting works. I am writing a
> state-by-state data page on open and closed primaries. Every state row must rest on an official source: a state statute
> section on the legislature's own site, or a state election office page. I will read every source myself, so give exact
> official URLs, not Ballotpedia, NCSL, Wikipedia or news. For each of these 17 jurisdictions: [list], give: (1) primary type
> for state and congressional primaries [...] and the plain rule; (2) can unaffiliated voters vote in a party primary, and
> must they declare or affiliate, and does that stick; (3) can a voter registered with one party switch for the primary, and
> the deadline in days before the primary, with the statute section; (4) whether each party sets its own rule [...]; (5)
> whether the presidential primary or caucus differs [...]; (6) same-day registration only if it changes who can vote in a
> primary. For each answer give the statute section number and the official URL. Flag anything that changed in 2024 to 2026
> [...]. Mark any inference as an inference.

## Type Labels

| Label | Meaning used here |
|---|---|
| `closed` | Only the party's registered members; others must join before primary day |
| `partial` | Party members plus unaffiliated voters (with or without joining at the polls), or each party decides |
| `open` | Any voter picks one party's ballot, no party registration, choice not recorded as affiliation |
| `open_record` | Any voter picks one party's ballot, and the choice is recorded or affiliates you |
| `top2` / `top4` | All candidates on one ballot; the top two or four advance |
| `other` | Fits none of these; the rule says what (Indiana) |

## Summary Table

Counts from these rows: closed 4 (CT, DE, DC, FL); partial 4 (AZ, CO, ID, KS); open 1 (HI); open_record 5 (AL, AR, GA,
IL, IA); top2 1 (CA); top4 1 (AK); other 1 (IN).

| State | Type | Unaffiliated voters | Party change deadline | Presidential differs? | Sources read |
|---|---|---|---|---|---|
| Alabama | open_record | Yes; no party registration | None; runoff locks you to your primary party | Yes, date: first Tuesday in March | SOS 2026 Voter Guide; SOS 2026 Poll Worker Guide (17-13-7, 17-13-7.1); Ala. Code 17-13-100 |
| Alaska | top4 | Yes, same ballot for all | Not needed | Yes: no state presidential primary; parties run caucuses | AS 15.25.010, 15.25.100; Division of Elections pages; 2026 unofficial results |
| Arizona | partial | Yes, choose one party ballot | 29 days (registration deadline) | Yes: independents cannot vote in the PPE | A.R.S. 16-467, 16-241, 16-120; SOS release June 21, 2024 |
| Arkansas | open_record | Yes, state the primary at the polls | None | No (preferential primary, 7-8-201) | Ark. Code 7-7-308, 7-7-203, 7-8-201 (SOS 2025 compilation); SOS "Voting in Arkansas" |
| California | top2 | Yes | Not needed (matters only for presidential) | Yes: party primary; parties decide NPP access | Cal. Const. art. II sec. 5; Elec. Code 13102; SOS primary and NPP history pages |
| Colorado | partial | Yes, stay unaffiliated | 22 days | Same rules, separate March date | C.R.S. 1-7-201, 1-2-219, 1-4-702, 1-4-1203, 1-4-1304 (2024 printout); SOS Primary FAQ |
| Connecticut | closed | Must enroll by noon the day before | Three months after switching | Separate date: first Tuesday in April | C.G.S. 9-431, 9-56, 9-59, 9-464; SOTS release May 7, 2026 |
| Delaware | closed | No (2026 AVR exception only) | Friday before last Saturday in May | Same closed rule; deadline 60 days before | 15 Del. C. 3110, 2049, 3189; Dept. of Elections primary page and FAQ |
| District of Columbia | closed (2026) | No; first-time same-day registrants may pick a party | 21 days | Not read | D.C. Code 1-1001.07; DCBOE FAQ; DCBOE 2026 Primary Voter Guide |
| Florida | closed (universal primary exception) | Only universal-primary and nonpartisan contests | 29 days | Same closed rule and deadline | Fla. Stat. 101.021, 97.055; DOS Closed Primary page |
| Georgia | open_record | Yes; no party registration | None; runoff locks you to your primary party | Separate date (2024: March 12, unverified) | SOS Elections FAQ (code not read) |
| Hawaii | open | Yes, pick on the ballot; not membership | None | Yes: no state primary; party caucuses | HRS 12-31; Office of Elections "Voting in Hawaii" |
| Idaho | partial (party decides) | Depends on party; may affiliate on election day | Candidate filing deadline (Mar. 13, 2026) | Not confirmed | Idaho Code 34-904A, 34-411A; SOS 2026 absentee form and calendar; H0638 status |
| Illinois | open_record | Yes, declare at the polls | None (0 days); town caucus exception | No (same primary ballot) | 10 ILCS 5/7-43, 7-44, 7-11 |
| Indiana | other | Yes, subject to party-support challenge | None | Not read | Election Division Dispatch May 2024; 2026 Voter Information Guide |
| Iowa | open_record | Yes, declare at the polls; sticks | 0 days (switch at polls) | Yes: party-run caucuses | Iowa Code 43.42, 43.41, 43.38, 43.4 (2026) |
| Kansas | partial | Yes, by joining at the polls (current party rules) | Candidate filing deadline (June 1) to certification | Yes: 2024 one-time state primary; otherwise party caucus or convention | K.S.A. 25-3301, 25-3304; SOS Election Standards ch. II; SOS releases |

## Sources Read, by State

Each quote was read on the page named, 2026-11-14.

### Alabama
- SOS 2026 Voter Guide: "Alabama does not have a party registration law; therefore, a citizen does not declare a political party preference when registering to vote." Voters "must declare which party's primary they wish to vote in and then must sign that party's poll list."
- SOS 2026 Poll Worker Guide: "A voter may participate in only one political party's primary [§ 17-13-7(a)]"; poll workers "must notate in the appropriate place on the poll book the party primary in which the voter participates."
- Ala. Code 17-13-100(a), Code of Alabama site in Chrome: presidential primaries "shall be held the first Tuesday in March each year in which a President is to be elected."

### Alaska
- AS 15.25.010: the primary "does not serve to determine the nominee of a political party or political group but serves only to narrow the number of candidates."
- AS 15.25.100(a): the director places "only the names of the four candidates receiving the greatest number of votes."
- Division of Elections, Election Information: "All candidates will be on the same ballot and all voters may vote for anyone regardless of registration affiliation." No state presidential primary; parties choose nominees by their own process.
- 2026 General Election Summary Report, **UNOFFICIAL**, run 11/13/2026 7:14 PM: Ballot Measure 2 (repeal) Yes 123,095 (46.48%), No 141,750 (53.52%), 400 of 400 precincts. Not certified; RCV tabulations due Nov. 18.

### Arizona
- A.R.S. 16-467(A): "there shall be provided a separate ballot for each party entitled to participate in the primary." (B): independents and no-party voters choose one qualifying party's ballot.
- A.R.S. 16-241(A): the PPE lets electors express preference for the candidate of the party "indicated as their preference by the record of their registration."
- A.R.S. 16-120(A): registration by "midnight of the twenty-ninth day preceding the date of the election."
- SOS release, June 21, 2024: "independent voters are not allowed by law to vote in the Presidential Preference Election."

### Arkansas
- Ark. Code 7-7-308(b) (SOS 2025 compilation): the official "shall mark next to the voter's name on the precinct voter registration list the party primary or other election in which the voter chooses to vote."
- 7-8-201: each party selecting national delegates "shall hold a preferential primary election in the state."
- SOS "Voting in Arkansas": you "must state the party primary in which you wish to vote"; "The election official records which party's ballot you choose."

### California
- Cal. Const. art. II, sec. 5(a) (leginfo, Chrome): "All voters may vote at a voter-nominated primary election for any candidate for congressional and state elective office without regard to the political party preference."
- Elections Code 13102(b): a voter without a party preference gets a nonpartisan ballot "unless the voter requests a ballot of a political party and that political party, by party rule duly noticed to the Secretary of State, authorizes" it.
- SOS NPP history page, March 5, 2024: "American Independent Party, letter dated October 10, 2023"; "Democratic Party, letter dated October 19, 2023"; "Libertarian Party, letter received October 17, 2023."

### Colorado
- C.R.S. 1-7-201(2.3) (2024 printout): an unaffiliated elector "is entitled to vote in the primary election of a major political party without affiliating."
- 1-2-219(1): change or withdraw affiliation "at any time up to and including the twenty-second day preceding a primary election."
- 1-4-1304(1.5)(c): a minor party "may prohibit unaffiliated electors from voting in the party's primary election," with notice "not less than seventy-five days prior."
- 1-4-702(1): a party may nominate by assembly if "at least three-fourths of the total membership of the party's state central committee votes" for it.
- 1-4-1203(1), (2)(b): presidential primary on a Tuesday set by the governor, no later than the third Tuesday in March; unaffiliated electors may vote; the 22-day rule applies.
- SOS Primary FAQ: unaffiliated voters "will remain unaffiliated"; "Returning two voted ballots will result in neither ballot counting."

### Connecticut
- C.G.S. 9-431(a) (Chrome): voting requires being on the party's enrollment list or, "if authorized by the state rules of such party filed pursuant to section 9-374," being unaffiliated.
- 9-56: enrollment filed in person "after twelve o'clock noon on the last business day before a primary" counts only after the primary.
- 9-59: after a transfer, no primary vote "for a period of three months from the date of the filing."
- 9-464: presidential primary "On the first Tuesday in April."
- SOTS release (May 7, 2026, updated July 15, 2026): "only registered members of a political party can vote in that party's primary"; unaffiliated voters "must enroll in a party by 12:00 p.m. the day before the primary."

### Delaware
- 15 Del. C. 3110: "An elector may vote in the primary election of a political party only if the elector is a duly registered voter" of that party.
- 2049(a): no changes "during the period from the last Saturday in May" "through the day of the primary election."
- 3189: last day to change before the presidential primary is "the sixtieth day before"; voters vote for a candidate "of the party of the voter's registration."
- Primary page: "Only Democrats can vote in the Democratic Primary." 2026 party-change deadline "Friday, May 29, 2026." AVR voters may designate a party "at any time up to and including September 15, 2026"; 2026 is the last election this applies.

### District of Columbia
- D.C. Code 1-1001.07(g)(4)(B): "A qualified elector shall not change the qualified elector's party affiliation after the 21st day preceding an election."
- DCBOE FAQ: primary voters "must be registered with one of the following parties" (Democratic, Republican, DC Statehood Green); "Only voters registering for the first time in DC may claim a party affiliation through Same-Day Registration."
- DCBOE 2026 Primary Voter Guide: for affiliated voters "ballots will include the contests for the Primary and Special Elections. For all other DC voters, the ballot will only include the Special Election contest." Party change deadline May 26, 2026.

### Florida
- Fla. Stat. 101.021: a voter "is entitled to vote the official primary election ballot of the political party designated in the elector's registration, and no other."
- 97.055(1)(a): "registration books must be closed on the 29th day before each election"; (1)(c) excludes nominating elections from late party changes.
- DOS Closed Primary page: closed rule applies "including a presidential preference primary election"; universal primary when all candidates share a party and the winner faces no opposition.

### Georgia
- SOS Elections FAQ (Chrome): "Voters do not register with a political party because Georgia is an open primary state"; choice "can be different each time"; party-ballot voters get the same party's runoff ballot; "their choice of political party ballot in the most recent primary election is publicly available information."

### Hawaii
- HRS 12-31: no one is "required to state a party preference or nonpartisanship as a condition of voting"; a voter votes "only for candidates of one party or only for nonpartisan candidates." Case note: selection "does not automatically make voter a party member."
- Office of Elections: "Hawaii voters do not declare a political affiliation with the voter registration"; "Hawaii does not conduct a presidential preference primary"; parties "independently conduct presidential caucuses."

### Idaho
- Idaho Code 34-904A(1): members "shall be allowed to vote only in the primary election of the political party"; (2) parties may open by notice "no later than the last Tuesday in the November prior to a primary election"; (8) voting in an open primary "shall not change or affect the elector's unaffiliated designation."
- 34-411A(1): changes no later than "the last day a candidate may file for partisan political office"; unaffiliated voters may affiliate "up to and including election day."
- SOS 2026 Election Calendar: "MAR. 13 Deadline to Change Political Party Affiliation or Become Unaffiliated Before the Primary Election [§34-411A]."
- SOS 2026 absentee request (EA-5, rev. 03/31/2026): Democratic ballot "regardless of your party affiliation"; Republican and Libertarian ballots "You MUST be affiliated with this party."
- Legislature, H0638 (2026): "to provide for a presidential primary election"; House passed 45-23-2 on 3/09; referred to Senate State Affairs 3/10.

### Illinois
- 10 ILCS 5/7-43 (Chrome): "No person shall be entitled to vote at a primary: (a) Unless he declares his party affiliations"; (c.5) bars someone who signed in at another party's town caucus "within 45 days before the first day of the calendar month in which the primary is held"; (d) "(Blank)."
- 7-44: the voter states party affiliation, a judge announces it, and "the official poll record shall be made"; the voter gets "one, and only one, primary ballot."
- 7-11: presidential candidates are printed "upon the primary ballot of his political party."

### Indiana
- Election Division, Dispatch, May 2024: voters do not register by party; "the voter develops a voting history based on which party's primary ballot was chosen"; IC 3-10-1-6 test (majority of the party's nominees last general election, or intent next time); "closer to a closed primary than an open one."
- 2026 Voter Information Guide: a voter challenged on party affiliation "is NOT issued a provisional ballot if the challenged voter is willing to complete and sign the challenged voter's affidavit (Form PRE-6)."

### Iowa
- Iowa Code 43.42(1) (2026): "Any registered voter may change or declare a party affiliation at the polls on election day."
- 43.38: the voter votes "the ballot of the party with which the elector is registered as affiliated, and shall receive no other ballot."
- 43.41: changes before the primary "before the close of registration."
- 43.4: precinct caucuses; "The state central committees of the political parties shall set the date for their caucuses."

### Kansas
- K.S.A. 25-3301(a): each party gives written notice by January 15 on "whether voters who are unaffiliated with such political party may vote in such party's primary election"; (c) a written statement at the polls "shall constitute a declaration of party affiliation."
- 25-3304(b): a change "cannot be filed during the time from the candidate filing deadline" "through the time when the primary election results are certified."
- SOS Election Standards ch. II (rev. 08/01/25): "Since 2004, the Republican Party has allowed party affiliates and unaffiliated voters who affiliate with the party"; the Democratic Party the same "Beginning with the 2014 election."
- SOS release May 28, 2026: change "by June 1"; "unaffiliated voters can affiliate with a party at the polls."
- SOS PPP page (Dec. 21, 2023): 2024 was a "one-time presidential preference primary"; another would "take an act of the legislature."

## Not Found or Conflicts

1. **Alaska, pending repeal.** Ballot Measure 2 (Nov. 3, 2026) would replace the top-four primary with party primaries. Unofficial
   results as of Nov. 13 show it failing 53.52% to 46.48%. Not certified. Don't publish the Alaska row until certification.
2. **District of Columbia, Initiative 83.** The 2026 primary was closed (DCBOE). News reports (leads, not sources) say the
   Council funded semi-open primaries in June 2026 for the 2028 cycle. The D.C. Code page for 1-1001.07 has conflicting
   applicability notes for other laws and does not mention Initiative 83. Find the codified Initiative 83 text and a DCBOE
   statement before the 2028 data is set. Effective date is the reason sources disagree.
3. **Delaware AVR date.** Two pages from the same office: the FAQ says Aug. 22, 2026; the Primary page says through Sept. 15,
   2026. Moot after 2026 (the office says it was the last election for this provision), so the row leaves it as a 2026-only note.
4. **Codes not read on the official code site:** Alabama 17-13-7 (tab failed; quoted via SOS guide), Georgia O.C.G.A.
   (LexisNexis), Indiana IC 3-10-1-6 (JavaScript viewer). Each row says so.
5. **Colorado** statutes are from the 2024 uncertified printout; 2025 and 2026 session changes not checked.
6. **Presidential rules not read:** DC, Idaho, Indiana; Georgia's date rests on a search result (SOS calendar PDF returned 403).
7. **Labeling calls for editors:** Connecticut (unaffiliated can enroll until the day before) is `closed`; Kansas (unaffiliated
   can join at the polls) is `partial`. Some references put these the other way. Pick one rule and apply it in all batches.
8. **Arkansas registration form** has a party field (Amendment 51), but no page read says it limits the primary ballot.

## Errors Caught in the Perplexity Answer

NCSL and EAC were not opened in this pass, so none are listed for them.

1. **Alaska.** Called the 2026 media packet proof of the "system actually used in 2026" and asked for a currency check, but
   never mentioned Ballot Measure 2 on the Nov. 3, 2026 ballot, which would repeal the top-four primary.
2. **District of Columbia.** Described the 2026 closed primary correctly but left out Initiative 83's semi-open primary
   provisions, passed in 2024 and reported funded for 2028.
3. **Arizona.** Cited the SOS "Information about Political Parties" page for the 16-241 rule that independents can't vote in
   the PPE. That page, as read, lists parties and contacts only. The rule is in 16-241 and an SOS release of June 21, 2024.
4. **Kansas.** Cited the SOS copy of 2023 Session Laws ch. 79 (SB 221) as "enacted text" of 25-3301 and 25-3304. The page only
   cross-references 25-3301; neither section's text is there. Read on ksrevisor.gov instead.
5. **Illinois.** Put the "must declare to vote" rule in 7-44; it is 7-43(a). 7-44 covers stating the party and the poll record.
6. **Arkansas.** Held back on whether the ballot choice is recorded and on presidential rules. 7-7-308(b) says the choice is
   marked on the voter list, and 7-8-201 sets the party preferential primary for presidential delegates.
7. **Idaho.** Said H0638 was "still listed in Senate State Affairs" without noting it passed the House 45-23-2 on 3/09. Minor,
   but it changes how close Idaho is to restoring a presidential primary.
8. **Colorado.** Said 2026 official guidance "identifies ... Libertarian as excluding" unaffiliated voters. The SOS FAQ read
   says only that Unity was the sole minor party holding a primary open to them; it doesn't name the Libertarian Party.
9. **Indiana.** Said the office calls it "neither a conventional open nor conventional closed system." The Election Division
   says it is "closer to a closed primary than an open one." Same idea, but the office's own lean was left out.
10. **Georgia and Iowa leads unverified.** The Georgia 2024 calendar URL it gave returned 403, and its claim that Iowa's
    office "calls it closed" rests on an SOS FAQ that returned 403. Neither is used.

## For Batch 2 (Kentucky to North Dakota)

These are leads from memory, not checked in this pass.

- Watch for 2024 to 2026 changes: Louisiana's new closed party primaries (already in `guide-runoff-elections.md`), Nevada's
  top-two ballot question in 2024, and any 2026 ballot measures like Alaska's.
- Several batch 2 states let unaffiliated voters choose a party ballot or let parties open their primaries (for example
  Maryland, Massachusetts, Nebraska, New Hampshire, North Carolina). Find the party notice for the current cycle, as Idaho's
  absentee form and Kansas's January 15 notices showed in this batch.
- Read the presidential rule for each state, not just the state primary (party-run caucuses, separate dates, different
  deadlines like Delaware's 60 days).
- Open NCSL and the EAC spreadsheet once, as leads, and list where they disagree with the rows.
- Codes behind LexisNexis (Mississippi, others) will block; plan on SOS pages and enacted bills.

## Labeling Rule (Decided Nov 14, 2026)

The label follows what an unaffiliated voter can do **on primary day**, because that is the question readers ask.

- **Closed:** an unaffiliated voter has to join a party before primary day. The row gives the deadline (Connecticut: noon the day before).
- **Partial:** an unaffiliated voter can get a party's ballot on primary day, by declaring or joining at the polls (Kansas), or because a party lets them in (Arizona, Colorado, Idaho by party).
- **Open, choice recorded:** any voter can pick a party's ballot on primary day, and the choice is recorded or changes their affiliation (Iowa, Illinois).
- The label is a summary. The row's plain rule says what each kind of voter can do, and that text is what readers act on.

Applies to all three batches. Alaska and DC stay on hold until certification (Ballot Measure 2) and an official DCBOE or D.C. Code page (Initiative 83).
