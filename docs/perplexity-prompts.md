# Perplexity Prompts for State Early Voting Dates

Run each group in a new Perplexity thread. Paste the JSON back to Claude; every "computed" or
"low" answer gets checked in Chrome against the official page before it goes into `site/states.js`.

## Prompt Template

```
For the US general election on Tuesday, November 3, 2026, find the early voting rules for each of these: {STATES}.

Use only official sources: the state Secretary of State or state election board website, or the state statute. Use news or nonprofit sites (Vote411, Vote.org) only to find the official page, never as the source.

For each state, return one JSON object with these fields:
- "abbr": two-letter code
- "kind": "in-person" if the state has in-person early voting (including in-person absentee or "early voting by mail ballot in person"), "mail" if the state runs all-mail elections, "none" if neither
- "start": first day of in-person early voting for this election, YYYY-MM-DD (for "mail" states, the date ballots are mailed out)
- "end": last day of in-person early voting, YYYY-MM-DD (for "mail" states, the return deadline)
- "varies": true if counties set their own early voting dates or hours and there is no single statewide window, otherwise false
- "officialUrl": the official page where a voter finds their own early voting location or hours
- "officialLabel": short display name for that link, like "sos.ks.gov"
- "sourceUrl": the official page that states the dates
- "note": one plain sentence a voter needs, under 20 words (for example, "Vote at any early voting site in your county.")
- "confidence": "high" if an official page states the 2026 dates directly, "computed" if you calculated them from the statute's rule (say which statute in the note), "low" otherwise

If a state's dates come from a rule like "20 days before the election," give the computed dates and set confidence to "computed." Do not guess. If you cannot find something, use null.

Return a JSON array only, followed by a list of any states where sources disagreed.
```

## Groups

1. Kansas, Missouri, New York, Washington, California, Colorado, Virginia, District of Columbia, North Carolina, Pennsylvania (Garrett's friends; run first)
2. Alabama, Alaska, Arizona, Arkansas, Connecticut, Delaware, Florida, Georgia, Hawaii, Idaho
3. Illinois, Indiana, Iowa, Kentucky, Louisiana, Maine, Maryland, Massachusetts, Michigan, Minnesota
4. Mississippi, Montana, Nebraska, Nevada, New Hampshire, New Jersey, New Mexico, North Dakota, Ohio, Oklahoma
5. Oregon, Rhode Island, South Carolina, South Dakota, Tennessee, Utah, Vermont, West Virginia, Wisconsin, Wyoming

Texas is done (SOS Important Election Dates page, read October 19, 2026: October 19 to 30).

## Round 2 (October 19, 2026, afternoon)

Group 3 never came back, and 23 states came back "low" or without an official page Chrome could
confirm. This version asks for the exact sentence from the official page, so the Chrome check is
quick, and separates excuse-only absentee states.

```
For the US general election on Tuesday, November 3, 2026, find the early voting dates for: {STATES}.

Use only official sources: the state Secretary of State or election board website, or the state statute. Use other sites only to find the official page.

For each state, return one JSON object:
- "abbr": two-letter code
- "kind": "in-person" (any in-person voting before Election Day open to all voters, including no-excuse in-person absentee), "mail" (the state mails every voter a ballot), "excuse-only" (in-person absentee only with an excuse), or "none"
- "start", "end": YYYY-MM-DD for the statewide window, or null
- "varies": true if counties or towns set their own days
- "officialUrl": the official page where a voter finds their early voting location or hours
- "officialLabel": short display name, like "sos.ks.gov"
- "sourceUrl": the official page that states the dates
- "quote": the exact sentence from sourceUrl that states the dates or the rule, copied word for word
- "note": one plain sentence a voter needs, under 20 words
- "confidence": "high" if the quote states the 2026 dates, "computed" if you calculated them from a rule (name the statute), "low" otherwise

Do not guess. Use null for anything you cannot find. Return a JSON array only.
```

- A: Illinois, Indiana, Iowa, Kentucky, Louisiana, Maine, Maryland, Massachusetts, Michigan, Minnesota
- B: Arizona, Arkansas, Connecticut, Georgia, Hawaii, Idaho, Montana, Nebraska
- C: New Jersey, New Mexico, North Dakota, Ohio, Oklahoma, South Dakota, Tennessee, Utah
- D: Vermont, West Virginia, Wisconsin, Wyoming, Alabama, Mississippi, New Hampshire

Verified in Chrome after round 1 (October 19): TX, KS, MO, NY, WA, CA, CO, VA, DC, NC, PA, AK, DE,
FL, NV, OR, RI, SC. Record of what each official page said: `build/verified-20261019.json`.
