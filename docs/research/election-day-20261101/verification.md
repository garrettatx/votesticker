# Pass 12 Verification (Nov 1, 2026)

Each new fact, the official page it comes from, how it was read, and the words on the page.

## Who to Call (faq.problem, 11 states)

| State | Source | Read with | Quote |
|---|---|---|---|
| TX | https://www.votetexas.gov/faq/index.html | curl | "Should you need additional information, please e-mail or call us at 1-800-252-VOTE(8683)." |
| CA | https://www.sos.ca.gov/elections/voter-bill-rights | curl | "If you believe you have been denied any of these rights, or you are aware of any election fraud or misconduct, please call the Secretary of State's confidential toll-free Voter Hotline at (800) 345-VOTE (8683)." |
| FL | https://dos.fl.gov/elections/ | Chrome | "VOTER ASSISTANCE HOTLINE Toll Free 1.866.308.6739 (in English and Español), available Monday - Friday, 8:00 a.m. - 5:00 p.m., with extended hours on the day registration ends and on Election Day for the upcoming statewide elections." |
| NC | https://www.ncsbe.gov/ | curl | "Contact North Carolina State Board of Elections (NCSBE) ... Phone: (919) 814-0700" |
| OR | https://sos.oregon.gov/voting/Pages/default.aspx | curl | "Oregon Secretary of State Elections Division ... Call Elections: 503-986-1518 Toll free: 1-866-673-VOTE (1-866-673-8683)" |
| WA | https://www.sos.wa.gov/elections | curl | "Washington Secretary of State Elections Division PO Box 40229 Olympia, WA 98504-0229 ... 1 (800) 448-4881" |
| NY | https://elections.ny.gov/contact-us | Chrome (curl 403) | "Election Operations Voter Information 1-800-367-8683" |
| IL | https://www.elections.il.gov/ | curl | "Springfield Office ... Phone: 217-782-4141 ... Chicago Office ... Phone: 312-814-6440" |
| MD | https://elections.maryland.gov/ | curl | "151 West Street, Suite 200 Annapolis, MD 21401 410-269-2840 \| 800-222-8683" |
| OH | https://www.ohiosos.gov/elections/frequently-asked-questions | Chrome (curl hit a security check) | "For questions about polling locations, curbside voting, or accessible ballot options, contact your county board of elections or the Ohio Secretary of State's office at 877-767-6446." |
| AK | https://www.elections.alaska.gov/contact/ | curl | "Telephone Contact Phone: (907) 465-4611 Toll-Free: (866) 952-8683 (within the U.S.)" |

## Home Page

- DOJ Voting Section (problem answer): https://www.justice.gov/crt/voting-section, Chrome. "The Voting Section enforces
  the civil provisions of the federal laws that protect the right to vote ..." and "Contact Tel: (202) 307-2767
  Toll-free: (800) 253-3931".
- Mail-voter answer: no new facts. Every office named is already verified on the site: San Francisco, Orange County, San
  Diego County and Santa Clara County (`build/state-facts/ca.json`), Oregon SOS, Washington SOS, Multnomah County and
  Colorado BallotTrax (home "Official Digital Stickers" list), El Paso County TX (`tx.json` sticker.mail).

## Not Used

- California Spanish voter hotline 800-232-8682 (Perplexity): not found on the English bill of rights page we read; left out.
- A federal "you can always ask for a provisional ballot" line: the uscode.house.gov and govinfo pages didn't return
  the text to curl, so the home answer points to each state page's verified provisional answer instead.
