# Election Day Readiness Audit: Summary (Oct 31, 2026, pass 4)

Builds: `VOTE_TODAY=2026-11-02` and `2026-11-03`, 51 states EN and ES (102 pages). Nov 2 and Nov 3 are the same
apart from the state list and three mail-deadline answers. Per-state extract: `extract-20261102.md`. Research:
`prompts.md`, `answer-g1.md` to `answer-g6.md` (Perplexity project, condensed; leads only).

| Check | Result |
|---|---|
| Poll hours (banner, summary box, FAQ) | All 51 match the official hours Perplexity cited and our earlier reads. Time zone splits already on the page: AL, FL (Gulf County), ID, IN, KS, KY, NE, ND, SD, TN, TX ("local time"), MI ("local time"). No change found. |
| In line at closing | 46 states had an answer. Added AL, NM, ND (read on the official page). MT (only a 2004 directive) and SD (the sentence is inside an HTML comment on sdsos.gov) left out. |
| Lookup links | 55 URLs: 42 return 200 to curl; the 13 that block curl (403 or cookie loop) all load in Chrome except New Hampshire, which shows a slider CAPTCHA to automated Chrome (a person passes it). Arizona's button went to the general Voters page; now points to the SOS 2026 Voting Locations page (every county's Election Day locations). Connecticut, Idaho and Kansas leads redirect to or match the URLs we use. |
| Vote centers vs. precincts | No wrong claims found; pages that mention vote centers (AZ, DC, NM, TX, WY, CO, CA) match. |
| ID | Unchanged since pass 3; banner carries no ID text. |
| Mail return on Election Day | Consistent with pass 2. Nov 3 late-mail answers for AL, CT, LA, NH, OK said "It may be" and pointed to a Monday hand-delivery window; now say what's left on Election Day. |
| Past-dated text (clause scan) | Fixed: OH, KY, WV, MO, WY "Where You Can Vote"; NJ, NY poll-hour answers (early voting dates); CT, MN, MA, MO registration answers; SC, CT, AL, OK mail return; NY mail.who; LA cure; OH, NY, MA, LA mail-request after-text (second stage); MA in-person absentee. `qa_stale`: Nov 2 30 to 26, Nov 3 24 to 15 (the rest are open windows or past tense). |
| Same-day registration | NC was missing the Election Day exception for newly eligible voters (ncsbe.gov Oct 30 release); added. Others match. |
| Past 7 days (Oct 24 to 31) | No court order, extended hours, deadline change or lookup outage found in any state. Arizona: SOS Voting Locations page warns "Flooding may affect roads and access to voting locations" (added). New Mexico: Oct 26 advisory on a weather-related temporary early voting closure, all resumed (no page change needed). |
